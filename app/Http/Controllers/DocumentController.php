<?php

namespace App\Http\Controllers;

use App\Document;
use App\Http\Requests\Document\StoreDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use http\Exception;
use Tymon\JWTAuth\Contracts\Providers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\File;

class DocumentController extends Controller
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny',Document::class);
        return DocumentResource::collection(Document::paginate(5));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreDocumentRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreDocumentRequest $request)
    {
        $this->authorize('create',Document::class);

            $documentPath = $request->file('document')->store('uploads/documents', 'public');

            $document = Document::create([
                'project_id' => $request->project_id,
                'name' => $request->file('document')->getClientOriginalName(),
                'description' => $request->description,
                'document' => $documentPath,
            ]);

        return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Document uploaded !',
                'data' => [
                    'item' => new DocumentResource($document),
                ]
            ], 200);
    }

    /**
     * @param Document $document
     * @return DocumentResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Document $document)
    {
        $authUser = $this->auth->user();
        $this->authorize('view', $document, Document::class);

        $documentToReturn = Document::findOrFail($document->id);
        return new DocumentResource($documentToReturn);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * @param UpdateDocumentRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateDocumentRequest $request, $id)
    {
        $authUser = $this->auth->user();
        $this->authorize('update', $authUser, Document::class);

        $documentFromDb = Document::findOrFail($id);

        if ($documentFromDb->update($request->validated()))
        {
            $updatedDocumentFromDb = Document::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Document updated',
                'data' => [
                    'item' => new DocumentResource($updatedDocumentFromDb),
                ]
            ], 200);
        }
    }

    /**
     * @param Document $document
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Document $document)
    {
        $authUser = $this->auth->user();
        $this->authorize('delete', $document, Document::class);

        unlink(storage_path('app/public/'.$document->document));
        if ($document->delete())
        {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Document deleted',
            ], 200);
        }
    }
}
