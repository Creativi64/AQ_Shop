<?php 

namespace Shipcloud;

/**
 * ShipmentIndexResponse
 */
class ShipmentIndexRequest extends JsonSerializable
{
	public static function create()
	{
		return new static();
	}

}