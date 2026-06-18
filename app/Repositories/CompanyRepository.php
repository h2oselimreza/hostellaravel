<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    protected $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function all()
    {
        return $this->company->latest()->get();
    }

    public function find($id)
    {
        return $this->company->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->company->create($data);
    }

    public function update($id, array $data)
    {
        $company = $this->find($id);
        $company->update($data);
        return $company;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}