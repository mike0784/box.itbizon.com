<?php

namespace Itbizon\Template\Utils;

use Bitrix\Crm\ContactTable;

class Transliteration
{
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            'NAME' => 'UF_CRM_1590389726',
            'LAST_NAME' => 'UF_CRM_1590396885',
            'SECOND_NAME' => 'UF_CRM_1590396906'
        ];
    }

    /**
     * @param string $string
     * @return string
     */
    public static function transliterate(string $string): string
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];
        return strtr($string, $converter);
    }


    /**
     * @param array $data
     * @return array
     */
    public static function getTransliterateData(array $data): array
    {
        $fields = [];
        foreach (array_keys(self::getMap()) as $key) {
            if (array_key_exists($key, $data)) {
                $fields[self::getMap()[$key]] = self::transliterate($data[$key]);
            }
        }
        return $fields;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public static function updateContactTransliterate(array $data): void
    {
        $id = $data['ID'];
        $transliterateField = self::getTransliterateData($data);

        if ($transliterateField) {
            $contact = ContactTable::update($id, $transliterateField);
            if (!$contact->isSuccess()) {
            throw new \Exception('Ошибка обновления полей транслитерации');
            }
        }
    }

}