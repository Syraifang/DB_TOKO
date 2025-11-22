<?php
class Respon {
    public bool $sukses;
    public string $pesan;
    public ?array $data;

    public function __construct(bool $sukses, string $pesan, ?array $data = null) {
        $this->sukses = $sukses;
        $this->pesan = $pesan;
        $this->data = $data;
    }
}
?>