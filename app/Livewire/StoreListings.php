<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;
use Livewire\WithPagination;

class StoreListings extends Component
{
    use WithPagination;

    public Store $store;
    public $filterType = 'all';

    public function mount(Store $store)
    {
        $this->store = $store;
    }

    public function setFilter($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->store->listings()->where('status', 'active');

        if ($this->filterType === 'auction') {
            $query->where('type', 'auction');
        } elseif ($this->filterType === 'direct_sale') {
            $query->where('type', 'direct_sale');
        } elseif ($this->filterType === 'hybrid') {
            $query->where('type', 'hybrid');
        }

        $listings = $query->latest()->paginate(12);

        return view('livewire.store-listings', [
            'listings' => $listings,
        ]);
    }
}
