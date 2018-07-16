<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

//Get All streamers
$app->get('/api/streamers', function(Request $request, Response $response) {
  $sql = "SELECT * FROM streamers";

  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $streamers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($streamers);

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }
});

//Get Single Streamer
$app->get('/api/streamer/{id}', function(Request $request, Response $response) {
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM streamers WHERE id = $id";

  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $streamer = $stmt->fetch(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($streamer);

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }

});

//Add Streamer
$app->post('/api/streamer/add', function(Request $request, Response $response) {
  $twitch = $request->getParam('twitch');
  $vids_number = $request->getParam('vids_number');
  $vods = $request->getParam('vods');
  $highlights = $request->getParam('highlights');
  $sponsors = $request->getParam('sponsors');
  $donation = $request->getParam('donation');
  $header = $request->getParam('header');
  $giveawayurl = $request->getParam('giveaway');

  $sql = "INSERT INTO streamers (twitch,vids_number,vods,highlights,sponsors,donation,header,giveawayurl)
  VALUES (:twitch,:vids_number,:vods,:highlights,:sponsors,:donation,:header,:giveawayurl)";

  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':twitch', $twitch);
    $stmt->bindParam(':vids_number', $vids_number);
    $stmt->bindParam(':vods', $vods);
    $stmt->bindParam(':highlights', $highlights);
    $stmt->bindParam(':sponsors', $sponsors);
    $stmt->bindParam(':donation', $donation);
    $stmt->bindParam(':header', $header);
    $stmt->bindParam(':giveawayurl', $giveawayurl);

    $stmt->execute();

    echo '{"notice": {"text": "Streamer Added"}}';

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }

});

// Delete Streamer
$app->delete('/api/streamer/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM streamers WHERE id = :id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(array('id' => $id));
        $db = null;
        echo '{"notice": {"text": "Streamer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Update Streamer
$app->put('/api/streamer/{id}/update', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $twitch = $request->getParam('twitch');
    $vids_number = $request->getParam('vids_number');
    $vods = $request->getParam('vods');
    $highlights = $request->getParam('highlights');
    $sponsors = $request->getParam('sponsors');
    $donation = $request->getParam('donation');
    $header = $request->getParam('header');
    $giveawayurl = $request->getParam('giveawayurl');

    $sql = "UPDATE streamers SET
				    twitch 	= :twitch,
            vids_number		= :vids_number,
            header = :header,
            donation = :donation,
            giveawayurl = :giveawayurl,
            vods = :vods,
            highlights = :highlights,
            sponsors = :sponsors
	          WHERE id = :id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':twitch', $twitch);
        $stmt->bindParam(':vids_number', $vids_number);
        $stmt->bindParam(':header', $header);
        $stmt->bindParam(':donation', $donation);
        $stmt->bindParam(':giveawayurl', $giveawayurl);
        $stmt->bindParam(':vods', $vods);
        $stmt->bindParam(':highlights', $highlights);
        $stmt->bindParam(':sponsors', $sponsors);

        $stmt->execute();

        echo '{"notice": {"text": "Settings Updated"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Get sponsors
$app->get('/api/streamer/{id}/sponsors', function(Request $request, Response $response) {
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM sponsors WHERE streamer_id = $id";

  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $sponsors = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($sponsors);

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }

});

//Add sponsor
$app->post('/api/streamer/{id}/sponsors/add', function(Request $request, Response $response) {
  $streamer_id = $request->getAttribute('id');
  $name = $request->getParam('name');
  $url = $request->getParam('url');
  $img = $request->getParam('img');


  $sql = "INSERT INTO sponsors (name, url, img, streamer_id) VALUES (:name, :url, :img, :streamer_id)";
  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':img', $img);
    $stmt->bindParam(':streamer_id', $streamer_id);

    $stmt->execute();

    echo '{"notice": {"text": "Sponsor Added"}}';

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }

});

//Add Giveaway
$app->post('/api/streamer/{id}/giveaway/add', function(Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $url = $request->getParam('url');

  $sql = "UPDATE streamers SET giveawayurl = :url WHERE id = :id";
  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    echo '{"notice": {"text": "Giveaway Added"}}';

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }

});


//Get Social
$app->get('/api/streamer/{id}/social', function(Request $request, Response $response) {
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM social WHERE streamer_id = $id";

  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $sponsors = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($sponsors);

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }

});
