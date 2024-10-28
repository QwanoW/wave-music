<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$queryParams = $_GET;

try {
  $db = new \App\Database();
  $table = new \App\Objects\Table($db->getConnection());

  // Prepare the base query
  $baseQuery = "SELECT * FROM album a";

  $params = [];

  $queryClauses = buildQueryClauses($queryParams, $params);

  $query = $baseQuery . $queryClauses;

  // Fetch all albums
  $albums = $table->fetchAll($query, $params);

  if (count($albums) === 1 && isset($queryParams['id'])) {
    $albums = $albums[0];
  }

  http_response_code(200);
  echo json_encode($albums);
} catch (\Exception $e) {
  http_response_code(500);

  echo json_encode(['message' => $e->getMessage()]);
}

function buildQueryClauses($queryParams, &$params)
{
  $whereConditions = [];
  $allowedParams = [
    'id' => 'a.id = :id',
    'search' => 'a.title LIKE :search',
  ];

  foreach ($allowedParams as $key => $condition) {
    if (isset($queryParams[$key])) {
      $whereConditions[] = $condition;
      $params[$key] = $key === 'search' ? '%' . $queryParams[$key] . '%' : $queryParams[$key];
    }

    if (isset($queryParams['limit'])) {
      $params['limit'] = $queryParams['limit'];
    }
  }

  $whereClause = !empty($whereConditions) ? ' WHERE ' . implode(' AND ', $whereConditions) : '';
  $limitClause = isset($queryParams['limit']) ? ' LIMIT :limit' : '';
  return $whereClause . $limitClause;
}

