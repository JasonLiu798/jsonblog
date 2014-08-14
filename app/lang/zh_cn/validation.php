<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => ":attribute 必须填写",
	"active_url"           => ":attribute 不是一个有效的URL.",
	"after"                => ":attribute 必须小于 :date",
	"alpha"                => ":attribute 只能使用字母",
	"alpha_dash"           => ":attribute 只能使用字母、数字、和下划线",
	"alpha_num"            => ":attribute 只能使用字母、数字",
	"array"                => "The :attribute must be an array.",
	"before"               => ":attribute必须大于:date",
	"between"              => array(
		"numeric" => "请修改数字 :attribute 大小在 :min 和 :max 之间",
		"file"    => ":attribute 大小必须在 :min 和 :max KB之间",
		"string"  => "请修改 :attribute 长度在 :min 和 :max 之间",
		"array"   => "请增加或减少 :attribute 数量在 :min 和 :max 之间"
	),
	"boolean"              => "The :attribute field must be true or false",
	"confirmed"            => "The :attribute confirmation does not match.",
	"date"                 => ":attribute 不是一个有效的日期",
	"date_format"          => "请修改 :attribute 日期格式为 :format",
	"different"            => "The :attribute and :other must be different.",
	"digits"               => "请修改 :attribute 为 :digits ",
	"digits_between"       => "请修改 :attribute 数字范围在 :min 和 :max 之间",
	"email"                => "请修改 :attribute 的格式",
	"exists"               => ":attribute 不存在",
	"image"                => " :attribute 必须为图像",
	"in"                   => "The selected :attribute is invalid.",
	"integer"              => "The :attribute must be an integer.",
	"ip"                   => "请修改 :attribute 格式",
	"max"                  => array(
		"numeric" => ":attribute 不能大于 :max",
		"file"    => ":attribute 不能大于 :max KB",
		"string"  => "请修改 :attribute 长度小于 :max ",
		"array"   => "The :attribute may not have more than :max items.",
	),
	"mimes"                => "The :attribute must be a file of type: :values.",
	"min"                  => array(
		"numeric" => "The :attribute must be at least :min.",
		"file"    => "The :attribute must be at least :min kilobytes.",
		"string"  => "请修改 :attribute 长度大于 :min ",
		"array"   => "The :attribute must have at least :min items.",
	),
	"not_in"               => "The selected :attribute is invalid.",
	"numeric"              => "The :attribute must be a number.",
	"regex"                => "请修改 :attribute 格式",
	"required"             => "请填写 :attribute ",
	"required_if"          => "The :attribute field is required when :other is :value.",
	"required_with"        => "The :attribute field is required when :values is present.",
	"required_with_all"    => "The :attribute field is required when :values is present.",
	"required_without"     => "The :attribute field is required when :values is not present.",
	"required_without_all" => "The :attribute field is required when none of :values are present.",
	"same"                 => "The :attribute and :other must match.",
	"size"                 => array(
		"numeric" => "The :attribute must be :size.",
		"file"    => "The :attribute must be :size kilobytes.",
		"string"  => "The :attribute must be :size characters.",
		"array"   => "The :attribute must contain :size items.",
	),
	"unique"               => ":attribute 已经被使用",
	"url"                  => "The :attribute format is invalid.",
	
	
	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'custom-message',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),
	
	'PASS_WRONG'=>'密码错误',
);
