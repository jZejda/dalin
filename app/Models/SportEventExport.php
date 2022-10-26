<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportEventExport extends Model
{
    use HasFactory;

    public const ENTRY_LIST_CATEGORY = 'eventEntryListCat';

    public const FILE_XML_IOF_V3 = 'xml_iof_v3_file';

    /**
     * Fillable fields.
     *
     * @var array<string>
     **/
    protected $fillable = [
        'title',
        'slug',
        'export_type',
        'sport_event_id',
        'sport_event_leg_id',
        'file_type',
        'result_path',
    ];
}
