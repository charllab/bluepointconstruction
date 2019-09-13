<?php

namespace modules\sproing\toolkit\twigextensions;

use craft\helpers\Template as TemplateHelper;

class ToolkitTwigExtension extends \Twig_Extension
{
	public function getName()
	{
		return 'Toolkit';
	}

	public function getFilters()
	{
		return [
			new \Twig_SimpleFilter('httpify', [$this, 'addHttpPrefix']),
			new \Twig_SimpleFilter('nl2li', [$this, 'nl2li']),
			new \Twig_SimpleFilter('timeDiff', [$this, 'timeDiff'], [
				'needs_environment' => true
			])
		];
	}

	public function addHttpPrefix($url)
	{
		if (strpos($url, '://') === false) {
			return 'https://' . $url;
		} else {
			return $url;
		}
	}

	public function nl2li($text)
	{
		return TemplateHelper::getRaw('<li>' . str_replace("\n", "</li><li>", $text) . '</li>');
	}

	public function timeDiff(\Twig_Environment $env, $date, $now = null)
	{
		$units = [
			'y' => 'year',
			'm' => 'month',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		];

		// Convert both dates to DateTime instances.
		$date = twig_date_converter($env, $date);
		$now = twig_date_converter($env, $now);

		// Get the difference between the two DateTime objects.
		$diff = $date->diff($now);

		// Check for each interval if it appears in the $diff object.
		foreach ($units as $attribute => $unit) {
			$count = $diff->$attribute;
			if (0 !== $count) {
				if ($count > 1)
					$unit .= 's';

				return $diff->invert ? "in $count $unit" : "$count $unit ago";
			}
		}
		return '';
	}
}