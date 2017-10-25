<?php

namespace MocOrm\Support;

/**
 * Implementation.
 */
class Log extends Model
{
    public function onLoad() {
        $this->setTableName('tb_log')
            ->setPrimaryKey()
            ->setIp()
            ->setSession();
    }

    public function setPrev($prev) {
        $this->prev = json_encode($prev);

        return $this;
    }

    public function getPrev() {
        return json_decode($this->prev);
    }

    public function setNext($next) {
        $this->next = json_encode($next);

        return $this;
    }

    public function getNext() {
        return json_decode($this->next);
    }

    public function setFunction($name) {
        $this->function = $name;

        return $this;
    }

    public function setSession() {
        $this->session = json_encode(@$_SESSION);

        return $this;
    }

    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    public function setIp() {
        $this->ip = @$_SERVER['REMOTE_ADDR'];

        return $this;
    }


}
