<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$queryParams = $_GET;

try {
  $db = new \App\Database();
  $table = new \App\Objects\Table($db->getConnection());

  // Prepare the base query
  $baseQuery = "SELECT * FROM artist a";

  $params = [];

  $queryClauses = buildQueryClauses($queryParams, $params);

  $query = $baseQuery . $queryClauses;

  // Fetch all artists
  $artists = $table->fetchAll($query, $params);

  if (count($artists) === 1 && isset($queryParams['id'])) {
    $artists = $artists[0];
  }

  http_response_code(200);
  echo json_encode($artists);
} catch (\Exception $e) {
  http_response_code(500);

  echo json_encode(['message' => $e->getMessage()]);
}

function buildQueryClauses($queryParams, &$params)
{
  $whereConditions = [];
  $allowedParams = [
    'id' => 'a.id = :id',
    'search' => 'a.name LIKE :search',
  ];

  foreach ($allowedParams as $key => $condition) {
    if (isset($queryParams[$key])) {
      $whereConditions[] = $condition;
      $params[$key] = $key === 'search' ? '%' . $queryParams[$key] . '%' : $queryParams[$key];
    }
  }

  $whereClause = !empty($whereConditions) ? ' WHERE ' . implode(' AND ', $whereConditions) : '';

  $limit = isset($queryParams['limit']) && $queryParams['limit'] !== 'no' ? (int)$queryParams['limit'] : null;
  $offset = isset($queryParams['offset']) ? (int)$queryParams['offset'] : 0;

  $limitClause = $limit !== null ? ' LIMIT ' . $limit : '';
  $offsetClause = $limit !== null ? ' OFFSET ' . $offset : '';

  return $whereClause . $limitClause . $offsetClause;
}
