<?php 

namespace Shipcloud;

/**
 * PickupTime
 */
class PickupTime  extends JsonSerializable
{

	protected $earliest;

	protected $latest;


	public function earliest() {
		return $this->earliest;
	}
    public function latest() {
        return $this->latest;
    }

    public function setEarliest($atomDate) {
        $this->earliest = $atomDate;
        return $this;
    }
    public function setLatest($atomDate) {
        $this->latest = $atomDate;
        return $this;
    }

}