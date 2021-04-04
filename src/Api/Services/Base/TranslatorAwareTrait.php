<?php

namespace App\Api\Services\Base;

use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorAwareTrait
{
  private TranslatorInterface $translator;

  private ?string $locale = null;

  public function initTranslator(TranslatorInterface $translator): void
  {
    $this->translator = $translator;
  }

  public function __(string $id, array $parameter = [], ?string $locale = null): string
  {
    return $this->trans($id, $parameter, $locale);
  }

  public function trans(string $id, array $parameter = [], ?string $locale = null): string
  {
    $domain = 'catroweb';
    $locale_with_underscore = $this->sanitizeLocale($locale);
    $this->locale = $locale_with_underscore;

    try {
      return $this->translator->trans($id, $parameter, $domain, $locale_with_underscore);
    } catch (Exception $e) {
      $this->locale = $this->getLocaleFallback();

      return $this->translator->trans($id, $parameter, $domain, $this->locale);
    }
  }

  public function sanitizeLocale(?string $locale = null): string
  {
    $locale = $this->removeTrailingNoiseOfLocale($locale);
    if ('' === $locale) {
      return $this->getLocaleFallback();
    }

    $locale = $this->normalizeLocaleFormatToLocaleWithUnderscore($locale);

    if ($this->isLocaleAValidLocaleWithUnderscore($locale)) {
      if (in_array($locale, $this->getSupportedLanguageCodes(), true)) {
        return $locale;
      }

      // Locale format is correct but the locale is not yet supported. Let's try without the regional code
      $locale = $this->mapLocaleWithUnderscoreToTwoLetterCode($locale);
    }

    if ($this->isLocaleAValidTwoLetterLocale($locale)) {
      // Two letter codes are not supported natively and must be mapped to an existing regional code

      return $this->mapTwoLetterCodeToLocaleWithUnderscore($locale);
    }

    // Format is definitely invalid; However we can just try the first two letter; Maybe we are lucky ;)
    $locale = strtolower(substr($locale, 0, 2));

    return $this->mapTwoLetterCodeToLocaleWithUnderscore($locale);
  }

  private function removeTrailingNoiseOfLocale(?string $locale): string
  {
    return explode(' ', trim($locale))[0];
  }

  public function isLocaleAValidLocaleWithUnderscore(string $locale): bool
  {
    return 1 === preg_match('/^([a-z]{2,3})(_[a-z,A-Z]+)$/', $locale);
  }

  public function isLocaleAValidTwoLetterLocale(string $locale): bool
  {
    return 1 === preg_match('/^([a-z]{2,3})$/', $locale);
  }

  public function normalizeLocaleFormatToLocaleWithUnderscore(string $locale): string
  {
    return str_replace('-', '_', $locale);
  }

  public function mapLocaleWithUnderscoreToTwoLetterCode(string $locale_with_underscore): string
  {
    return explode('_', $locale_with_underscore)[0];
  }

  /**
   * There is no need to generate the mapping on every request.
   * In case new locales are added this mapping should be updated manually.
   *
   * Python code:
   * ```
   * import os
   * my_dir = os.listdir(r"PATH_TO_DIR_REPLACE!")
   * my_dir.sort()
   * for item in my_dir:
   *     locale_with_underscore = (item.split('.'))[1]
   *     two_letter_code = (locale_with_underscore.split('_'))[0]
   *     print("case '" + two_letter_code + "':")
   *     print("  return '" + locale_with_underscore + "';")
   * ```
   *
   * @return string locale_with_underscore
   */
  public function mapTwoLetterCodeToLocaleWithUnderscore(string $two_letter_code): string
  {
    switch ($two_letter_code) {
      // Custom Mapping to provide better translations
      case 'en':
        return 'en_UK';
      case 'pt':
        return 'pt_BR';

      // Autogenerated mapping without Custom:
      case 'af':
        return 'af_ZA';
      case 'ar':
        return 'ar_SA';
      case 'az':
        return 'az_AZ';
      case 'bg':
        return 'bg_BG';
      case 'bn':
        return 'bn_BD';
      case 'bs':
        return 'bs_BA';
      case 'ca':
        return 'ca_ES';
      case 'chr':
        return 'chr_US';
      case 'cs':
        return 'cs_CZ';
      case 'da':
        return 'da_DK';
      case 'de':
        return 'de_DE';
      case 'el':
        return 'el_GR';
      case 'es':
        return 'es_ES';
      case 'fa':
        return 'fa_AF';
      case 'fi':
        return 'fi_FI';
      case 'fr':
        return 'fr_FR';
      case 'gl':
        return 'gl_ES';
      case 'gu':
        return 'gu_IN';
      case 'ha':
        return 'ha_HG';
      case 'he':
        return 'he_IL';
      case 'hi':
        return 'hi_IN';
      case 'hr':
        return 'hr_HR';
      case 'hu':
        return 'hu_HU';
      case 'id':
        return 'id_ID';
      case 'ig':
        return 'ig_NG';
      case 'it':
        return 'it_IT';
      case 'ja':
        return 'ja_JP';
      case 'ka':
        return 'ka_GE';
      case 'kab':
        return 'kab_KAB';
      case 'kk':
        return 'kk_KZ';
      case 'kn':
        return 'kn_IN';
      case 'ko':
        return 'ko_KR';
      case 'lt':
        return 'lt_LT';
      case 'mk':
        return 'mk_MK';
      case 'ml':
        return 'ml_IN';
      case 'ms':
        return 'ms_MY';
      case 'nl':
        return 'nl_NL';
      case 'no':
        return 'no_NO';
      case 'pl':
        return 'pl_PL';
      case 'ps':
        return 'ps_AF';
      case 'ro':
        return 'ro_RO';
      case 'ru':
        return 'ru_RU';
      case 'sd':
        return 'sd_PK';
      case 'si':
        return 'si_LK';
      case 'sk':
        return 'sk_SK';
      case 'sl':
        return 'sl_SI';
      case 'sq':
        return 'sq_AL';
      case 'sr':
        return 'sr_Latn';
      case 'sv':
        return 'sv_SE';
      case 'sw':
        return 'sw_KE';
      case 'ta':
        return 'ta_IN';
      case 'te':
        return 'te_IN';
      case 'th':
        return 'th_TH';
      case 'tl':
        return 'tl_PH';
      case 'tr':
        return 'tr_TR';
      case 'tw':
        return 'tw_TW';
      case 'uk':
        return 'uk_UA';
      case 'ur':
        return 'ur_PK';
      case 'uz':
        return 'uz_UZ';
      case 'vi':
        return 'vi_VN';
      case 'zh':
        return 'zh_CN';
    }

    return $this->getLocaleFallback();
  }

  /**
   * There is no need to query all Languages on every request. They change rarely.
   * In case new locales are added this mapping should be updated here.
   *
   * Python code:
   * ```
   * import os
   * my_dir = os.listdir(r"PATH_TO_DIR_REPLACE!")
   * my_dir.sort()
   * for item in my_dir:
   *     locale_with_underscore = (item.split('.'))[1]
   *     print("'" + locale_with_underscore + "',")
   * ```
   */
  public function getSupportedLanguageCodes(): array
  {
    return [
      'af_ZA',
      'ar_SA',
      'az_AZ',
      'bg_BG',
      'bn_BD',
      'bs_BA',
      'ca_ES',
      'chr_US',
      'cs_CZ',
      'da_DK',
      'de_DE',
      'el_GR',
      'en',
      'en_AU',
      'en_CA',
      'en_GB',
      'es_ES',
      'fa_AF',
      'fa_IR',
      'fi_FI',
      'fr_FR',
      'gl_ES',
      'gu_IN',
      'ha_HG',
      'he_IL',
      'hi_IN',
      'hr_HR',
      'hu_HU',
      'id_ID',
      'ig_NG',
      'it_IT',
      'ja_JP',
      'ka_GE',
      'kab_KAB',
      'kk_KZ',
      'kn_IN',
      'ko_KR',
      'lt_LT',
      'mk_MK',
      'ml_IN',
      'ms_MY',
      'nl_NL',
      'no_NO',
      'pl_PL',
      'ps_AF',
      'pt_BR',
      'pt_PT',
      'ro_RO',
      'ru_RU',
      'sd_PK',
      'si_LK',
      'sk_SK',
      'sl_SI',
      'sq_AL',
      'sr_Latn',
      'sr_SP',
      'sv_SE',
      'sw_KE',
      'ta_IN',
      'te_IN',
      'th_TH',
      'tl_PH',
      'tr_TR',
      'tw_TW',
      'uk_UA',
      'ur_PK',
      'uz_UZ',
      'vi_VN',
      'zh_CN',
      'zh_TW',
    ];
  }

  public function getLocale(): string
  {
    return $this->locale ?? $this->getLocaleFallback();
  }

  public function getLocaleFallback(): string
  {
    return 'en';
  }
}
