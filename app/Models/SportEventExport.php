<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportEventExport
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $export_type
 * @property Carbon|null $start_time
 * @property int|null $sport_event_id
 * @property int|null $sport_event_leg_id
 * @property string $file_type
 * @property string $result_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin IdeHelperSportEventExport
 */
class SportEventExport extends Model
{
    use HasFactory;

    public const ENTRY_LIST_CATEGORY = 'eventEntryListCat';
    public const RESULT_LIST_CATEGORY = 'resultEntryListCat';

    public const FILE_XML_IOF_V3 = 'xml_iof_v3_file';

    protected $fillable = [
        'title',
        'slug',
        'export_type',
        'start_time',
        'sport_event_id',
        'sport_event_leg_id',
        'file_type',
        'result_path',
    ];

    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s',
        ];
}
