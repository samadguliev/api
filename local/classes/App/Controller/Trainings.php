<?php

namespace App\Controller;

use \Bitrix\Main\Type\DateTime;

class Trainings extends Base
{
    use ProjectTrait;

    public function create(Array $params)
    {
        global $DB;
        $realParams = array_merge(
            $params,
            $this->getPostParamsFromJson()
        );

        $model = new \App\Model\Trainings;
        return $model->add($realParams);
    }

    public function getList()
    {
        $model = new \App\Model\Trainings;
        $list = $model->getList([]);
        $list = array_map(function ($item) {
            $item['date'] = $item['date']->toString();
            return $item;
        }, $list);
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

    public function delete($id)
    {
        $model = new \App\Model\Trainings;
        return $model->delete($id);
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
