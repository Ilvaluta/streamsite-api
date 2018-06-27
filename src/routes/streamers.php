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
  $twitter = $request->getParam('twitter');
  $vids_number = $request->getParam('vids_number');

  $sql = "INSERT INTO streamers (twitch,twitter,vids_number) VALUES (:twitch,:twitter,:vids_number)";

  try {
    // Get DB Object
    $db = new db();
    //Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':twitch', $twitch);
    $stmt->bindParam(':twitter', $twitter);
    $stmt->bindParam(':vids_number', $vids_number);

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
$app->put('/api/streamer/update', function(Request $request, Response $response){
    $id = $request->getParam('id');
    $twitch = $request->getParam('twitch');
    $twitter = $request->getParam('twitter');
    $vids_number = $request->getParam('vids_number');

    $sql = "UPDATE streamers SET
				    twitch 	= :twitch,
				    twitter 	= :twitter,
            vids_number		= :vids_number
	          WHERE id = :id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':twitch', $twitch);
        $stmt->bindParam(':twitter', $twitter);
        $stmt->bindParam(':vids_number', $vids_number);

        $stmt->execute();

        echo '{"notice": {"text": "Info Updated"}';
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
