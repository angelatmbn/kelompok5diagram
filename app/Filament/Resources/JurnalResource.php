<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JurnalResource\Pages;
use App\Filament\Resources\JurnalResource\RelationManagers;
use App\Models\Jurnal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// tambahan
use App\Models\Coa;
use App\Models\JurnalDetail;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

use Filament\Tables\Columns\TextColumn;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\DB;

class JurnalResource extends Resource
{
    protected static ?string $model = Jurnal::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    // tambahan buat label Jurnal Umum
    protected static ?string $navigationLabel = 'Jurnal Umum';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Deskripsi Jurnal')
                    ->schema([
                        DatePicker::make('tgl')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        TextInput::make('no_referensi')
                            ->label('No Referensi')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->required()
                            ->minLength(3),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Detail Jurnal')
                    ->schema([
                        Repeater::make('items')
                            ->label('Detail Jurnal')
                            ->relationship('jurnaldetail')
                            ->schema([
                                Select::make('coa_id')
                                    ->label('Akun')
                                    ->options(Coa::all()->pluck('nama_akun', 'id'))
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->kode_akun} - {$record->nama_akun}")
                                    ->searchable()
                                    ->required()
                                    ->live(),
                                TextInput::make('debit')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(),
                                TextInput::make('credit')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(),
                                Textarea::make('keterangan')
                                    ->label('Keterangan')
                                    ->rows(2),
                            ])
                            ->columns(1)
                            ->required()
                            ->minItems(2)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $totalDebit = collect($state)->sum(function ($item) {
                                    return floatval($item['debit'] ?? 0);
                                });
                                $totalCredit = collect($state)->sum(function ($item) {
                                    return floatval($item['credit'] ?? 0);
                                });
                                
                                if ($totalDebit !== $totalCredit) {
                                    $set('error', 'Total debit dan kredit harus sama');
                                }
                            }),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl')
                    ->date()
                    ->sortable(),
                TextColumn::make('jurnaldetail.coa.kode_akun')
                    ->label('Kode Akun')
                    ->searchable(),
                TextColumn::make('deskripsi')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('jurnaldetail.debit')
                    ->label('Total Debit')
                    ->formatStateUsing(function ($record) {
                        $debit = $record->jurnaldetail()->sum('debit');
                        return rupiah($debit);
                    })
                    ->alignment('end'),
                TextColumn::make('jurnaldetail.credit')
                    ->label('Total Kredit')
                    ->formatStateUsing(function ($record) {
                        $credit = $record->jurnaldetail()->sum('credit');
                        return rupiah($credit);
                    })
                    ->alignment('end'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tgl')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tgl', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJurnals::route('/'),
            'create' => Pages\CreateJurnal::route('/create'),
            'edit' => Pages\EditJurnal::route('/{record}/edit'),
        ];
    }
}