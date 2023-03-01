<?php

namespace app\controllers;

use app\models\Client;
use app\models\Loan;
use app\services\CheckApiData;
use DateTimeImmutable;
use Exception;
use yii\base\Controller;

class ApiController extends Controller
{
    /**
     * @var CheckApiData
     */
    private CheckApiData $apiDataServices;


    /**
     * @param $id
     * @param $module
     * @param $config
     */
    public function __construct($id, $module, $config = [])
    {
        $this->apiDataServices = new CheckApiData();
        parent::__construct($id, $module, $config);
    }

    public function actionApi(array $fullClient =
                              [
//                                  [
//                                      'offer' => 'monetkin_msk',
//                                      'channel' => 'demo_name',
//                                      'lead_number' => 2345,
//                                      'credit_sum' => 100000,
//                                      'last_name' => 'Калашников',
//                                      'first_name' => 'Василий',
//                                      'patronymic' => 'Владимирович',
//                                      'mobile_phone' => 9275554433,
//                                      'birth_date' => '1980-01-12',
//                                      'email' => 'name@site.ru',
//                                      'passport_number' => 5555666666,
//                                      'passport_fms' => 'Отделением УФМС России г. Москва',
//                                      'passport_code' => 111222,
//                                      'passport_date' => '2000-10-07',
//                                      'birth_place' => 'город Москва',
//                                      'reg_region_name' => 'Москва',
//                                      'reg_city_name' => 'Москва',
//                                      'reg_city_street' => 'ул. Ефремова',
//                                      'reg_house' => '21',
//                                      'reg_housing' => '453',
//                                      'reg_flat' => 92,
//                                      'act_region_code' => 1,
//                                      'fact_city_name' => 'Москва',
//                                      'fact_city_street' => 'ул. Ефремова',
//                                      'fact_house' => '21',
//                                      'fact_housing' => '453',
//                                      'fact_flat' => 92,
//                                      'issuing_reg' => 1,
//                                  ],
//                                  [
//                                      'offer' => 'monetkin',
//                                      'channel' => 'demo_name',
//                                      'lead_number' => 2345,
//                                      'credit_sum' => 10000,
//                                      'last_name' => 'добронравов',
//                                      'first_name' => 'Радион',
//                                      'patronymic' => 'миронович',
//                                      'mobile_phone' => 927554324,
//                                      'birth_date' => '1980-03-12',
//                                      'email' => 'fg@site.ru',
//                                      'passport_number' => 5555666666,
//                                      'passport_fms' => 'Отделением УФМС России г. Москва',
//                                      'passport_code' => 11156222,
//                                      'passport_date' => '2000-07-10',
//                                      'birth_place' => 'город Москва',
//                                      'reg_region_name' => 'Москва',
//                                      'reg_city_name' => 'Москва',
//                                      'reg_city_street' => 'ул. Ефремова',
//                                      'reg_house' => '21',
//                                      'reg_housing' => '453',
//                                      'reg_flat' => 92,
//                                      'act_region_code' => 1,
//                                      'fact_city_name' => 'Москва',
//                                      'fact_city_street' => 'ул. Ефремова',
//                                      'fact_house' => '21',
//                                      'fact_housing' => '453',
//                                      'fact_flat' => 92,
//                                      'issuing_reg' => 1,
//                                  ],
//                                  [
//                                      'offer' => '',
//                                      'channel' => '',
//                                      'lead_number' => 2345,
//                                      'credit_sum' => 10000,
//                                      'last_name' => '',
//                                      'first_name' => '',
//                                      'patronymic' => '',
//                                      'mobile_phone' => 927554324,
//                                      'birth_date' => '',
//                                      'email' => '',
//                                      'passport_number' => 5555 - 666666,
//                                      'passport_fms' => '',
//                                      'passport_code' => 11156222,
//                                      'passport_date' => '',
//                                      'birth_place' => '',
//                                      'reg_region_name' => '',
//                                      'reg_city_name' => '',
//                                      'reg_city_street' => '',
//                                      'reg_house' => '',
//                                      'reg_housing' => '',
//                                      'reg_flat' => 92,
//                                      'act_region_code' => 6,
//                                      'fact_city_name' => '',
//                                      'fact_city_street' => '',
//                                      'fact_house' => '',
//                                      'fact_housing' => '',
//                                      'fact_flat' => 92,
//                                      'issuing_reg' => 1,
//                                  ],

                              ]
    )
    {

        $pasport = ['passport_number' => '',
            'passport_fms' => '',
            'passport_code' => '',
            'passport_date' => '',
            'reg_region_name' => '',
            'reg_city_name' => '',
            'reg_city_street' => '',
            'reg_house' => '',
            ];

        $loan = [

            'issuing_reg' => $this->integer(length: 2)->notNull(),
        ];

        echo '<pre>';
        $date = json_encode($fullClient);

        try {
            $validDataApi = $this->apiDataServices->checkApiData($date);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
//        var_dump($validDataApi);
        exit();
        $client = new Client();
        foreach ($validDataApi as $item) {

            $check = $client->findBySql(
                sql: 'SELECT * FROM `client` WHERE `name` =:name AND `reg`=:reg AND `city` =:city AND `date`=:date',
                params: [
                    ':name' => $item->name,
                    ':reg' => $item->reg,
                    ':city' => $item->city,
                    ':date' => $item->date,
                ])->all();

            if (empty($check[0])) {
                $client->setName($item->name);
                $client->setReg($item->reg);
                $client->setCity($item->city);
                $client->setDate($item->date);
                $client->save();
                $client->refresh();
                $clientId = $client;
            } else {
                $clientId = $check[0];
            }

            $loan = new Loan();
            $loan->setLoanSum($item->loanSum);
            $loan->setDateCreate((new DateTimeImmutable())->format('Y-m-d'));
            $loan->setClientId($clientId->getId());
            $loan->save();
            $loan->refresh();

            $clientId->trigger(Client::EVENT_NEW_USER);
        }
//        echo '<pre>'
//        var_dump($loan);
//        var_dump($client);
    }
}