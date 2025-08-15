<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\Techno;
use App\Models\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;


class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $builder= array();

        foreach(Techno::where('product_id',$data['id'])->get()->sortBy('order') as $t)
        {
            $techno= array( 'id'         => $t->id,
                            'title'      => $t->title,
                            'order'      => $t->order,
                            'status'     => array()
                           );

            foreach(Status::where('techno_id',$t->id)->get()->sortBy('order') as $s)
            {
                $status= array( 'id'         => $s->id,
                                'title'      => $s->title,
                                'order'      => $s->order,
                                'level'      => $s->level
                                );
                array_push($techno['status'], $status);
            }
                   
            array_push($builder, $techno);
        }
        $data['techno']= $builder;
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        saveTechProcess($data['techno'], $record->id);
     
        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
