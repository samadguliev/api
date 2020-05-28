<?php

namespace App\Controller;

use \Bitrix\Main\Type\DateTime;

class Enrollment extends Base
{
    use ProjectTrait;

    public function create(Array $params)
    {
        global $DB;
        $realParams = array_merge(
            $params,
            $this->getPostParamsFromJson()
        );

        $model = new \App\Model\Enrollment;
        return $model->add($realParams);
    }

    public function getList()
    {
        $model = new \App\Model\Enrollment;
        $trainingModel = new \App\Model\Trainings;

        $ids = [];
        $list = $model->getList();
        foreach ($list as $item) {
            $ids[] = $item['trainingId'];
        }

        $trainingList = $trainingModel->getList([
            'filter' => ['id' => $ids],
        ]);

        $list = array_map(function ($item) {
            $item['date'] = $item['date']->toString();
            return $item;
        }, $trainingList);

        if(!$list) {
            return [];
        }
        return $list;
    }

    public function getById($id)
    {
        $model = new \App\Model\Trainings;
        $row = $model->getRow([
            'filter' => [
                'id' => $id
            ]
        ]);
        $row['date'] = $row['date']->toString();
        return $row;
    }

    public function delete()
    {
        $realParams = $this->getPostParamsFromJson();

        $model = new \App\Model\Enrollment;
        $list = $model->getList([
           'filter' => ['trainingId' => $realParams['trainingId']]
        ]);
        $ids = [];
        foreach ($list as $item) {
            $ids[] = $item['id'];
        }

        foreach ($ids as $id) {
            $model->delete($id);
        }
        return true;
    }

    public function update($id, array $params = [])
    {
        $model = new \App\Model\Trainings;

        $realParams = array_merge(
            $params,
            $this->getPostParamsFromJson()
        );

        return $model->update($id, $realParams);
    }
}
