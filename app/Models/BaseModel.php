<?php

namespace App\Models;

use App\Traits\UnIdTrait;
use CodeIgniter\Model;

/**
 * BaseModel - root for all entity models in the ERP.
 *
 * Provides:
 *  - automatic un_id generation via UnIdTrait
 *  - soft delete + timestamps by default
 *  - findByUnId helper
 *  - lookup of internal id from un_id (used to bridge to FK columns
 *    while keeping the public API un_id-only)
 */
abstract class BaseModel extends Model
{
    use UnIdTrait;

    protected $useAutoIncrement = true;
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $dateFormat       = 'datetime';
    protected $returnType       = 'array';

    protected $beforeInsert = ['attachUnId'];

    /**
     * Find a row by its public un_id.
     */
    public function findByUnId(string $unId): ?array
    {
        $row = $this->where('un_id', $unId)->first();
        return $row ?: null;
    }

    /**
     * Resolve the internal numeric id from an un_id, or null.
     */
    public function idFromUnId(string $unId): ?int
    {
        $row = $this->select('id')->where('un_id', $unId)->first();
        if (! $row) {
            return null;
        }
        return (int) (is_array($row) ? $row['id'] : $row->id);
    }

    /**
     * Soft-delete by un_id. Returns true if a row was affected.
     */
    public function deleteByUnId(string $unId): bool
    {
        $id = $this->idFromUnId($unId);
        if ($id === null) {
            return false;
        }
        return (bool) $this->delete($id);
    }
}
