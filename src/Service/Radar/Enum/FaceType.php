<?php
namespace App\Service\Radar\Enum;

/**
 * Тип лица
 */
enum FaceType :string {

    case HUMAN = 'human';
    case MAN = 'man';
    case WOMAN = 'woman';
    case BOY = 'boy';
    case GIRL = 'girl';
    case ASIAN_MAN= 'asian_man';
    case ASIAN_WOMAN = 'asian_woman';
    case ASIAN_BOY= 'asian_boy';
    case ASIAN_GIRL = 'asian_girl';

    /**
     * Название
     *
     * @return string
     */
    public function title() :string {
        return match ($this){
            self::HUMAN => 'человек',
            self::MAN => 'мужчина',
            self::WOMAN => 'женщина',
            self::BOY => 'мальчик',
            self::GIRL => 'девочка',
            self::ASIAN_MAN => 'азиат',
            self::ASIAN_WOMAN => 'азиаточка',
            self::ASIAN_BOY => 'азиатик',
            self::ASIAN_GIRL => 'азиаточка',
        };
    }

}