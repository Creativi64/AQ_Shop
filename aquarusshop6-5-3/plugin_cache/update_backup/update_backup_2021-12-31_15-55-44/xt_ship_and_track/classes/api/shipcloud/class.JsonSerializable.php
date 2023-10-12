<?php 

namespace Shipcloud;


class JsonSerializable implements \JsonSerializable{

	public function jsonSerialize()
	{
		$vars = get_object_vars($this);

		foreach($vars as $k => $v)
		{
			if(empty($v))
			{
				unset($vars[$k]);
			}
		}

		return $vars;
	}

}