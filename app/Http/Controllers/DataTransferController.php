<?php

namespace App\Http\Controllers;

use App\Services\DataTransfer\CsvExporter;
use App\Services\DataTransfer\CsvImporter;
use App\Services\DataTransfer\EntityRegistry;
use Illuminate\Http\Request;

class DataTransferController extends Controller
{
    public function index()
    {
        $entities = EntityRegistry::all();

        return view('data-transfer.index', compact('entities'));
    }

    public function export(string $entity, CsvExporter $exporter)
    {
        if (! EntityRegistry::exists($entity)) {
            abort(404);
        }

        return $exporter->export($entity);
    }

    public function template(string $entity, CsvExporter $exporter)
    {
        if (! EntityRegistry::exists($entity)) {
            abort(404);
        }

        return $exporter->template($entity);
    }

    public function import(Request $request, string $entity, CsvImporter $importer)
    {
        if (! EntityRegistry::exists($entity)) {
            abort(404);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $result = $importer->import($entity, $request->file('file'));

        return redirect()->route('admin.data-transfer.index')->with('import_result', [
            'entity' => $entity,
            ...$result,
        ]);
    }
}
