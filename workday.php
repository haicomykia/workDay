<?php

  require_once '../vendor/autoload.php';

  $today = (new DateTimeImmutable())->format('Y/m/d');
  var_dump(getWorkday('2021/09/23', 1));

  /**
   * 日付と日数を指定してn日後の営業日を求める.
   * 引数で指定した日は含めない.
   *
   * @param string $start_date 起算日
   * @param int    $day        日数
   *
   * @return string n日後の営業日
   */
  function getWorkday(string $start_date, int $day): string
  {
      $date = new DateTime($start_date);
      if ($day === 0) {
          return $date;
      }

      if ($day < 0) {
          for ($i = 0;;) {
              if (!isHoliday($date->format('Y/m/d')) && --$i <= $day) {
                  return $date->format('Y/m/d');
              }
              $date->modify('-1day');
          }
      }

      if ($day > 0) {
          for ($i = 0;;) {
              if (!isHoliday($date->format('Y/m/d')) && ++$i >= $day) {
                  return $date->format('Y/m/d');
              }
              $date->modify('+1day');
          }
      }
  }

  /**
   * 引数に与えた日付が祝日 or 土日か判定する.
   *
   * @return 休日ならtrue
   */
  function isHoliday(string $date): bool
  {
      $date = new DateTimeImmutable($date);

      // 年末年始、GW、お盆
      $special_holidays = [
        new DateTimeImmutable('2021/4/30'),
        new DateTimeImmutable('2021/5/3'),
        new DateTimeImmutable('2021/5/4'),
        new DateTimeImmutable('2021/5/5'),
        new DateTimeImmutable('2021/5/6'),
        new DateTimeImmutable('2021/5/7'),
        new DateTimeImmutable('2021/8/9'),
        new DateTimeImmutable('2021/8/10'),
        new DateTimeImmutable('2021/8/11'),
        new DateTimeImmutable('2021/8/12'),
        new DateTimeImmutable('2021/8/13'),
        new DateTimeImmutable('2021/12/29'),
        new DateTimeImmutable('2021/12/30'),
        new DateTimeImmutable('2021/12/31'),
      ];
      if (in_array($date, $special_holidays)) {
          return true;
      }

      // 祝日判定
      $year = $date->format('Y');
      $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');
      if ($holidays->isHoliday($date)) {
          return true;
      }

      // 土日判定
      return $date->format('w') % 6 === 0;
  }
