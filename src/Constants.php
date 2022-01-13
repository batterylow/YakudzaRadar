<?php

namespace App;

/**
 * Глобальные константы приложения
 */
class Constants {

    /**
     * Режим обработки данных
     *
     * APP_MODE_INLINE - Ответ генерируется и отправляется сразу
     * APP_MODE_MESSAGE - Запрос становится в очередь брокера сообщений и по готовности возвращается пользователю
     */
    const APP_MODE_INLINE = 'inline';
    const APP_MODE_MESSAGE = 'message';

}