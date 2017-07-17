<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */
namespace LaravelBoot\Foundation\Passport\Controllers;

use Illuminate\Http\Response;
use Illuminate\Validation\Factory as ValidationFactory;
use Laravel\Passport\ClientRepository;
use LaravelBoot\Foundation\Routing\Abstracts\Controller;

/**
 * Class ClientsController.
 */
class ClientsController extends Controller
{
    /**
     * @var \Laravel\Passport\ClientRepository
     */
    protected $clients;

    /**
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $validation;

    /**
     * ClientsController constructor.
     *
     * @param \Laravel\Passport\ClientRepository $clients
     * @param \Illuminate\Validation\Factory     $validation
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(ClientRepository $clients, ValidationFactory $validation)
    {
        parent::__construct();
        $this->clients = $clients;
        $this->validation = $validation;
    }

    /**
     * Destroy handler.
     *
     * @param $clientId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($clientId)
    {
        if (!$this->request->user()->clients->find($clientId)) {
            return new Response('', 404);
        }
        $this->clients->delete($this->request->user()->clients->find($clientId));
    }

    /**
     * Index handler.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $userId = $this->request->user()->getKey();

        return $this->clients->activeForUser($userId)->makeVisible('secret');
    }

    /**
     * Store handler.
     *
     * @return \Laravel\Passport\Client * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store()
    {
        $this->validation->make($this->request->all(), [
            'name'     => 'required|max:255',
            'redirect' => 'required|url',
        ])->validate();

        return $this->clients->create(
            $this->request->user()->getKey(),
            $this->request->name,
            $this->request->redirect
        )->makeVisible('secret');
    }

    /**
     * Update handler.
     *
     * @param $clientId
     *
     * @return \Illuminate\Http\Response|\Laravel\Passport\Client
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($clientId)
    {
        if (!$this->request->user()->clients->find($clientId)) {
            return new Response('User not found!', 404);
        }
        $this->validation->make($this->request->all(), [
            'name'     => 'required|max:255',
            'redirect' => 'required|url',
        ])->validate();

        return $this->clients->update(
            $this->request->user()->clients->find($clientId),
            $this->request->name, $this->request->redirect
        );
    }
}
