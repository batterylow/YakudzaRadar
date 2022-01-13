<?php
namespace App\Service\Radar\Enum;

/**
 * Пол
 */
enum Gender :string {

    case MALE = 'male';
    case FEMALE = 'female';

    /**
     * Название
     *
     * @return string
     */
    public function title() :string {
        return match ($this){
            self::MALE => 'Мужчина',
            self::FEMALE => 'Женщина',
        };
    }

}