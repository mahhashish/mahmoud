<?php
/**
 * Fetch a resource
 *
 * @param  mixed $id
 * @return ApiProblem|mixed
 */
public function fetch($id) {
   $project = new \Project_Model_Project();
   $projectMapper = new \Project_Model_ProjectMapper();
   $projectMapper->fetchRow($project, [
      // Change later in proper authentication
      'accountId = ?' => 1,
      'projectId = ?' => $id,
   ]);
   return $project->toArray();
}

/**
 * Fetch all or a subset of resources
 *
 * @param  array $params
 * @return ApiProblem|mixed
 */
public function fetchAll($params = array())
{
   $projectCollection = new \Project_Model_Collection();
   $projectMapper = new \Project_Model_ProjectMapper();
   $projectMapper->fetchAll(
      $projectCollection,
      '\Project_Model_Project',
      array (
         // Change later in proper authentication
         'accountId = ?' => 1,
      )
   );
   return $projectCollection->toArray();
}