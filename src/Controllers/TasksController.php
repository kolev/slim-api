<?php
namespace App\Controllers;

use \Respect\Validation\Validator as v;

class TasksController
{
    protected $db;
    protected $validator;

    public function __construct($c)
    {
        $this->db = $c->get('db');
        $this->validator = $c->get('validator');
    }

    public function index($request, $response, $args)
    {
        $sth = $this->db->prepare("SELECT * FROM tasks");
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $response->withJson($result);
    }

    public function show($request, $response, $args)
    {
        $sth = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $sth->execute(['id' => $args['id']]);
        $result = $sth->fetchObject();

        if ($result)
            return $response->withJson($result);
        else
            return $response->withStatus(404);
    }

    public function create($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $validator = $this->validator->validate($request, $this->validations());

        if ($validator->isValid()) {
            $sth = $this->db->prepare("INSERT INTO tasks (title, status, created_at) VALUES (:title, :status, now())");
            $sth->execute([
                'title' => $params['title'],
                'status' => $params['status']
            ]);
            $output['id'] = $this->db->lastInsertId();
            return $response->withJson($output);
        } else {
            return $response->withJson($validator->getErrors(), 400);
        }
    }

    public function update($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $validator = $this->validator->validate($request, $this->validations());

        if ($validator->isValid()) {
            $sth = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
            $sth->execute(['id' => $args['id']]);
            $result = $sth->fetchObject();

            if ($result) {
                $sth = $this->db->prepare("UPDATE tasks SET title = :title, status = :status WHERE id = :id");
                $sth->execute([
                    'id' => $args['id'],
                    'title' => $params['title'],
                    'status' => $params['status']
                ]);
                return $response->withStatus(200);
            } else
                return $response->withStatus(404);
        } else {
            return $response->withJson($validator->getErrors(), 400);
        }
    }

    public function delete($request, $response, $args)
    {
        $sth = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $sth->execute(['id' => $args['id']]);
        $result = $sth->fetchObject();

        if ($result) {
            $sth = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
            $sth->execute(['id' => $args['id']]);
            return $response->withStatus(200);
        } else
            return $response->withStatus(404);
    }

    private function validations()
    {
        return [
            'title' => v::notEmpty(),
            'status' => v::notEmpty()
        ];
    }
}
