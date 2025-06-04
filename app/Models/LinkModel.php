<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkModel extends Model
{
    protected $useSoftDeletes = true;
    protected $table = 'links';
    protected $primaryKey = 'id';
    protected $deletedFields = 'deleted_at';
    protected $allowedFields = [
        'user_id',
        'original_url',
        'alias_url',
        'shortened_url',
        'encryption',
        'password',
        'is_encrypted',
        'expiry',
        'expired_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public function getTotalLinks()
    {
        return $this->withDeleted()
            ->countAllResults();
    }

    public function getLinksByUserId($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function getLinksByUserIdAndDate($userId, $start_date = null, $end_date = null)
    {
        // $this->where('user_id', $userId);
if ($start_date && !preg_match('/\d{4}-\d{2}-\d{2}/', $start_date)) {
            throw new \Exception("Invalid start date format.");
        }

        if ($end_date && !preg_match('/\d{4}-\d{2}-\d{2}/', $end_date)) {
            throw new \Exception("Invalid end date format.");
        }

        $builder = $this->where('user_id', $userId);

        if ($start_date) {
            $builder->where('created_at >=', $start_date . ' 00:00:00');
        }
        if ($end_date) {
            $builder->where('created_at <=', $end_date . ' 23:59:59');
        }

        return $builder->findAll();
    }

    public function getEncryptedLinksByUserIdAndDate($userId, $startDate = null, $endDate = null)
    {
        $this->where('user_id', $userId)
            ->where('password IS NOT NULL', null, false);

        if ($startDate) {
            $this->where('DATE(created_at) >=', $startDate);
        }
        if ($endDate) {
            $this->where('DATE(created_at) <=', $endDate);
        }

        return $this->findAll();
    }

    public function getActiveLinks($currentDateTime)
    {
        return $this->where('expired_at IS NULL OR expired_at >=', $currentDateTime)->findAll();
    }

    public function getExpiredLinks($currentDateTime)
    {
        return $this->where('expired_at <', $currentDateTime)->findAll();
    }
}
