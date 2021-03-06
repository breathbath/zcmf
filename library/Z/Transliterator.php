<?php
class Z_Transliterator
{
	public static $cyr_eng = array(
	"а" => "a",
	"б" => "b",
	"в" => "v",
	"г" => "g",
	"д" => "d",
	"е" => "e",
	"ё" => "yo",
	"ж" => "zh",
	"з" => "z",
	"и" => "i",
	"й" => "j",
	"к" => "k",
	"л" => "l",
	"м" => "m",
	"н" => "n",
	"о" => "o",
	"п" => "p",
	"р" => "r",
	"с" => "s",
	"т" => "t",
	"у" => "u",
	"ф" => "f",
	"х" => "h",
	"ц" => "ts",
	"ч" => "ch",
	"ш" => "sh",
	"щ" => "shch",
	"ъ" => "",
	"ы" => "y",
	"ь" => "",
	"э" => "e",
	"ю" => "yu",
	"я" => "ya",
	"А" => "A",
	"Б" => "B",
	"В" => "V",
	"Г" => "G",
	"Д" => "D",
	"Е" => "E",
	"Ё" => "Yo",
	"Ж" => "Zh",
	"З" => "Z",
	"И" => "I",
	"Й" => "J",
	"К" => "K",
	"Л" => "L",
	"М" => "M",
	"Н" => "N",
	"О" => "O",
	"П" => "P",
	"Р" => "R",
	"С" => "S",
	"Т" => "T",
	"У" => "U",
	"Ф" => "F",
	"Х" => "H",
	"Ц" => "Ts",
	"Ч" => "Ch",
	"Ш" => "Sh",
	"Щ" => "Shch",
	"Ъ" => "",
	"Ы" => "Y",
	"Ь" => "",
	"Э" => "E",
	"Ю" => "Yu",
	"Я" => "Ya",
	" " => "_",
	"-" => "_",
	"~" => "_",
	"!" => "_",
	"@" => "_",
	"#" => "_",
	"$" => "_",
	"%" => "_",
	"^" => "_",
	"&" => "_",
	"*" => "_",
	"(" => "_",
	")" => "_",
	"+" => "_",
	"=" => "_",
	"Ё" => "_",
	"№" => "_",
	";" => "_",
	":" => "_",
	"?" => "_",
	"{" => "_",
	"}" => "_",
	"[" => "_",
	"]" => "_",
	"\\" => "_",
	"|" => "_",
	"/" => "_"
	);
	/**
	 * преобразовать из кириллицы в транслит
	 * @param string $val
	 * @return string
	 */
	public static function translateCyr($val)
	{
		return strtr($val, self::$cyr_eng);
	}
}
