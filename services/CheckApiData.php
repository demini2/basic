<?php

namespace app\services;

use DateTimeImmutable;
use Exception;
use stdClass;

/**
 * Сервис проверки данный пришедших через API
 */
class CheckApiData
{
    const CHECK_FIO = "/^([а-яА-ЯЁё' -]{1,30})$/u";
    const CHECK_PHONE = '/^[0-9\-\+]{6,11}$/u';
    const CHECK_STRING = "/^([а-яА-ЯЁё' .-]{3,120})$/u";
    const CHECK_BIRTH_PLACE = "/^([а-яА-ЯЁё' -]{3,300})$/u";


    private array $apiKey = [
        'offer' => '',
        'channel' => '',
        'lead_number' => '',
        'credit_sum' => '',
        'last_name' => '',
        'first_name' => '',
        'patronymic' => '',
        'mobile_phone' => '',
        'birth_date' => '',
        'passport_number' => '',
        'passport_fms' => '',
        'passport_code' => '',
        'passport_date' => '',
        'reg_region_name' => '',
        'reg_city_name' => '',
        'reg_city_street' => '',
        'reg_house' => '',
        'act_region_code' => '',
        'fact_city_name' => '',
        'fact_city_street' => '',
        'fact_house' => '',
        'issuing_reg' => '',
    ];

    /**
     * @param string $apiJson
     * @return array|stdClass
     * @throws Exception
     */
    public function checkApiData(string $apiJson): array|stdClas
    {
        $re = [];
        /** @var stdClass $dateApi */
        $dateApi = json_decode($apiJson);
        if (is_object($dateApi)) {
            $dateApi = json_decode(json_encode($dateApi), true);
            $re = array_diff_key($this->apiKey, $dateApi);
        } elseif (is_array($dateApi)) {
            foreach ($dateApi as $date) {
                $date = json_decode(json_encode($date), true);
                $re = array_diff_key($this->apiKey, $date);
            }
        }

        if (!empty($re)) {
            $pole = '';
            foreach ($re as $item => $value) {
                $pole .= $item . ' ';
            }
            throw new Exception('отсутствуют обязательные поля: ' . $pole . ',');
        }

        if (is_array($dateApi)) {

            /** @var stdClass $datum */
            foreach ($dateApi as $datum) {
                $this->checkIssuingReg($datum->act_region_code, $datum->issuing_reg);
                $this->checkName($datum->first_name);
                $this->checkPatronymic($datum->patronymic);
                $this->checkLastName($datum->last_name);
                $this->checkDate($datum->birth_date);
                $this->checkLimitSum($datum->credit_sum);
                $this->checkMobilePhone($datum->mobile_phone);
                $this->checkEmail($datum->email);
                $this->checkPassportNumber($datum->passport_number);
                $this->checkPassportFms($datum->passport_fms);
                $this->checkPassportCode($datum->passport_code);
                $this->checkDate($datum->passport_date);
                $this->checkBirthPlace($datum->birth_place);
                $this->checkRegRegionName($datum->reg_region_name);
                $this->checkCityName($datum->reg_city_name);
                $this->checkCityStreet($datum->reg_city_street);
                $this->checkHouse($datum->reg_house);
                $this->checkHousing($datum->reg_housing);
                $this->checkFlat($datum->reg_flat);
                $this->checkCityName($datum->fact_city_name);
                $this->checkCityStreet($datum->fact_city_street);
                $this->checkHouse($datum->fact_house);
                $this->checkHousing($datum->fact_housing);
                $this->checkFlat($datum->fact_flat);
            }

            return $dateApi;
        }

        $this->checkIssuingReg($dateApi->act_region_code, $dateApi->issuing_reg);
        $this->checkName($dateApi->first_name);
        $this->checkPatronymic($dateApi->patronymic);
        $this->checkLastName($dateApi->last_name);
        $this->checkDate($dateApi->birth_date);
        $this->checkLimitSum($dateApi->credit_sum);
        $this->checkMobilePhone($dateApi->mobile_phone);
        $this->checkEmail($dateApi->email);
        $this->checkPassportNumber($dateApi->passport_number);
        $this->checkPassportFms($dateApi->passport_fms);
        $this->checkPassportCode($dateApi->passport_code);
        $this->checkDate($dateApi->passport_date);
        $this->checkBirthPlace($dateApi->birth_place);
        $this->checkRegRegionName($dateApi->reg_region_name);
        $this->checkCityName($dateApi->reg_city_name);
        $this->checkCityStreet($dateApi->reg_city_street);
        $this->checkHouse($dateApi->reg_house);
        $this->checkHousing($dateApi->reg_housing);
        $this->checkFlat($dateApi->reg_flat);
        $this->checkCityName($dateApi->fact_city_name);
        $this->checkCityStreet($dateApi->fact_city_street);
        $this->checkHouse($dateApi->fact_house);
        $this->checkHousing($dateApi->fact_housing);
        $this->checkFlat($dateApi->fact_flat);

        return $dateApi;
    }

    /**
     * Проверка имени
     *
     * @param string $name
     * @return void
     * @throws Exception
     */
    private function checkName(string $name): void
    {
        if (!preg_match(self::CHECK_FIO, $name)) {
            throw new Exception('ошибка валидации имени: ' . $name);
        }
    }

    /**
     * Проверка отчества
     *
     * @param string $patronymic
     * @return void
     * @throws Exception
     */
    private function checkPatronymic(string $patronymic): void
    {
        if (!preg_match(self::CHECK_FIO, $patronymic)) {
            throw new Exception('ошибка валидации отчества: ' . $patronymic);
        }
    }

    /**
     * Проверка фамилии
     *
     * @param string $surname
     * @return void
     * @throws Exception
     */
    private function checkLastName(string $surname): void
    {
        if (!preg_match(self::CHECK_FIO, $surname)) {
            throw new Exception('ошибка валидации фамилии: ' . $surname);
        }
    }

    /**
     * Проверка даты
     *
     * @param string $birthDate
     * @return void
     * @throws Exception
     */
    private function checkDate(string $birthDate): void
    {
        if (!DateTimeImmutable::createFromFormat('Y-m-d', $birthDate)) {
            throw new Exception('ошибка валидации даты рождения: ' . $birthDate);
        }
    }

    /**
     * Проверка суммы
     *
     * @throws Exception
     */
    private function checkLimitSum(int $sum): void
    {
        if ($sum > 10000 && $sum < 100000) {
            throw new Exception('ошибка валидации лимита суммы: ' . $sum);
        }
    }

    /**
     * Проверка телефона
     *
     * @param int $mobilePhone
     * @return void
     * @throws Exception
     */
    private function checkMobilePhone(int $mobilePhone): void
    {
        if (!preg_match(self::CHECK_PHONE, $mobilePhone)) {
            throw new Exception('ошибка валидации номера телефона: ' . $mobilePhone);
        }
    }

    /**
     * Проверка email
     *
     * @param string|null $email
     * @return void
     * @throws Exception
     */
    private function checkEmail(?string $email): void
    {
        if (null === $email) {
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('ошибка валидации email: ' . $email);
        }
    }

    /**
     * Проверка номера и серии паспорта
     *
     * @param int $passportNumber
     * @return void
     * @throws Exception
     */
    private function checkPassportNumber(int $passportNumber): void
    {
        if (!mb_strlen($passportNumber) === 10) {
            throw new Exception('ошибка валидации номер паспорта: ' . $passportNumber);
        }
    }

    /**
     * Проверка кода подразделения
     *
     * @param int $passportCode
     * @return void
     * @throws Exception
     */
    private function checkPassportCode(int $passportCode): void
    {
        if (!mb_strlen($passportCode) === 6) {
            throw new Exception('ошибка валидации подразделения паспорта: ' . $passportCode);
        }
    }

    /**
     * Проверка номера квартиры
     *
     * @param int|null $flat
     * @return void
     * @throws Exception
     */
    private function checkFlat(?int $flat): void
    {
        if (null === $flat) {
            return;
        }
        if (!$flat > 0 && $flat < 1000) {
            throw new Exception('ошибка валидации квартиры: ' . $flat);
        }
    }

    /**
     * Проверка кем выдан паспорт
     *
     * @param string $passportFms
     * @return void
     * @throws Exception
     */
    private function checkPassportFms(string $passportFms): void
    {
        if (!preg_match(self::CHECK_STRING, $passportFms)) {
            throw new Exception('ошибка валидации кем выдан паспорт: ' . $passportFms);
        }
    }

    /**
     * Проверка региона по прописке
     * @param string $regRegionName
     * @return void
     * @throws Exception
     */
    private function checkRegRegionName(string $regRegionName): void
    {
        if (!preg_match(self::CHECK_STRING, $regRegionName)) {
            throw new Exception('ошибка валидации региона по прописке: ' . $regRegionName);
        }
    }

    /**
     * @param string $cityName
     * @return void
     * @throws Exception
     */
    private function checkCityName(string $cityName): void
    {
        if (!preg_match(self::CHECK_STRING, $cityName)) {
            throw new Exception('ошибка валидации города по прописке: ' . $cityName);
        }
    }

    /**
     * Проверка улицы прописки
     *
     * @param string $cityStreet
     * @return void
     * @throws Exception
     */
    private function checkCityStreet(string $cityStreet): void
    {
        if (!preg_match(self::CHECK_STRING, $cityStreet)) {
            throw new Exception('ошибка валидации улицы по прописке: ' . $cityStreet);
        }
    }

    /**
     * Проверка дома
     *
     * @param string $house
     * @return void
     * @throws Exception
     */
    private function checkHouse(string $house): void
    {
        if (empty($house)) {
            throw new Exception('ошибка валидации дома: ' . $house);
        }
    }

    /**
     * Проверка строения
     *
     * @param string|null $housing
     * @return void
     * @throws Exception
     */
    private function checkHousing(?string $housing): void
    {
        if (null === $housing) {
            return;
        }
    }

    /**
     * Проверка места рождения
     *
     * @param string|null $birthPlace
     * @return void
     * @throws Exception
     */
    private function checkBirthPlace(?string $birthPlace): void
    {
        if (null === $birthPlace) {
            return;
        }

        if (!preg_match(self::CHECK_BIRTH_PLACE, $birthPlace)) {
            throw new Exception('ошибка валидации места рождения: ' . $birthPlace);
        }
    }

    /**
     * Проверка региона проживания
     *
     * @param int $reg
     * @return void
     * @throws Exception
     */
    public function checkRegion(int $reg): void
    {
        $arrayReg = [
            '1' => 'москва',
            '2' => 'московская об.',
            '3' => 'санкт-петербург',
            '4' => 'ленинградская об.',
            '5' => 'краснодарский край',
        ];
        if (!array_key_exists($reg, $arrayReg)) {
            throw new Exception('ошибка региона проживания: ' . $reg);
        }
    }

    /**
     * Проверка региона проживания и региона выдачи
     *
     * @param int $reg
     * @param int $issuingReg
     * @return void
     * @throws Exception
     */
    public function checkIssuingReg(int $reg, int $issuingReg): void
    {
        $this->checkRegion($reg);

        if ($issuingReg !== $reg) {
            throw new Exception('ошибка региона выдачи заема: ' . $issuingReg);
        }
    }

}