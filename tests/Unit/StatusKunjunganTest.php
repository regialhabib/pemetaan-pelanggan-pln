<?php

namespace Tests\Unit;

use App\Enums\StatusKunjungan;
use PHPUnit\Framework\TestCase;

class StatusKunjunganTest extends TestCase
{
    public function test_status_advances_in_order_and_stops_when_done(): void
    {
        $this->assertSame(StatusKunjungan::Diproses, StatusKunjungan::BelumDikunjungi->next());
        $this->assertSame(StatusKunjungan::SudahDikunjungi, StatusKunjungan::Diproses->next());
        $this->assertNull(StatusKunjungan::SudahDikunjungi->next());
    }

    public function test_label_is_human_readable(): void
    {
        $this->assertSame('Belum dikunjungi', StatusKunjungan::BelumDikunjungi->label());
    }
}
