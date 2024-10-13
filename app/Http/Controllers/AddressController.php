<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AddressResource;
use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact): Contact
    {
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
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

        return $contact;
    }

    private function getContactAddress(Contact $contact, int $idAddress): Address
    {
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response(
                [
                    'errors' => [
                        'message' => [
                            'Address not found'
                        ]
                    ]
                ],
                404
            ));
        }

        return $address;
    }

    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getContactAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getContactAddress($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }
}
