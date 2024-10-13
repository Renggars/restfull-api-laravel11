<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContactResource;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();


        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();

        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response(
                [
                    'errors' => [
                        'message' => [
                            'Contact not found'
                        ]
                    ]
                ],
                404
            ));
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response(
                [
                    'errors' => [
                        'message' => [
                            'Contact not found'
                        ]
                    ]
                ],
                404
            ));
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response(
                [
                    'errors' => [
                        'message' => [
                            'Contact not found'
                        ]
                    ]
                ],
                404
            ));
        }

        $contact->delete();
        return response()->json([
            'message' => 'Contact deleted'
        ], 200);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $contacts = Contact::query()->where('user_id', $user->id);
        //     ->where('first_name', 'like', "%{$request->get('q')}%")
        //     ->orWhere('last_name', 'like', "%{$request->get('q')}%")
        //     ->orWhere('email', 'like', "%{$request->get('q')}%")
        //     ->orWhere('phone', 'like', "%{$request->get('q')}%")
        //     ->get();

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->get('name');
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%");
                });
            }

            $email = $request->get('email');
            if ($email) {
                $builder->where(function (Builder $builder) use ($email) {
                    $builder->where('email', 'like', $email);
                });
            }

            $phone = $request->get('phone');
            if ($phone) {
                $builder->where(function (Builder $builder) use ($phone) {
                    $builder->where('phone', 'like', $phone);
                });
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
