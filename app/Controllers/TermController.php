<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

final class TermController extends Controller
{
    /**
     * Term repository.
     */
    private TermRepository $terms;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        TermRepository $terms
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->terms = $terms;
    }

    /**
     * Display all terms.
     */
    public function index(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        return $this->view(
            'terms.index',
            [
                'title' => 'Academic Terms',
                'terms' => $this->terms->allOrdered(),
            ]
        );
    }

    /**
     * Show the create term form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        return $this->view(
            'terms.create',
            [
                'title' => 'Create Term',
            ]
        );
    }

    /**
     * Store a new term.
     */
    public function store(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $data = [
            'name' => trim(
                (string) $request->input('name')
            ),
            'sort_order' => (int) $request->input(
                'sort_order',
                1
            ),
            'status' => $request->input('status') === 'inactive'
                ? 'inactive'
                : 'active',
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:3|max:50',
                'sort_order' => 'required|integer|min:1|max:99',
            ]
        );

        if ($validator->fails()) {
            $this->session->flash(
                '_old_input',
                $data
            );

            $this->session->flash(
                '_errors',
                $validator->errors()
            );

            return $this->redirect(
                '/SchoolERP/public/terms/create'
            );
        }

        try {
            $this->terms->create($data);
        } catch (\PDOException $exception) {
            if (
                $exception->getCode() === '23000'
                || str_contains(
                    strtolower(
                        $exception->getMessage()
                    ),
                    'duplicate'
                )
            ) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'name' => 'A term with this name already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/terms/create'
                );
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Term created successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/terms'
        );
    }

    /**
     * Show the edit term form.
     */
    public function edit(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $term = $this->terms->find($id);

        if ($term === null) {
            return Response::notFound();
        }

        return $this->view(
            'terms.edit',
            [
                'title' => 'Edit Term',
                'term' => $term,
            ]
        );
    }

    /**
     * Update a term.
     */
    public function update(
        Request $request,
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $term = $this->terms->find($id);

        if ($term === null) {
            return Response::notFound();
        }

        $data = [
            'name' => trim(
                (string) $request->input('name')
            ),
            'sort_order' => (int) $request->input(
                'sort_order',
                $term->sort_order
            ),
            'status' => $request->input('status') === 'inactive'
                ? 'inactive'
                : 'active',
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:3|max:50',
                'sort_order' => 'required|integer|min:1|max:99',
            ]
        );

        if ($validator->fails()) {
            $this->session->flash(
                '_old_input',
                $data
            );

            $this->session->flash(
                '_errors',
                $validator->errors()
            );

            return $this->redirect(
                '/SchoolERP/public/terms/'
                . $id
                . '/edit'
            );
        }

        try {
            $updated = $this->terms->updateTerm(
                $id,
                $data
            );
        } catch (\PDOException $exception) {
            if (
                $exception->getCode() === '23000'
                || str_contains(
                    strtolower(
                        $exception->getMessage()
                    ),
                    'duplicate'
                )
            ) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'name' => 'A term with this name already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/terms/'
                    . $id
                    . '/edit'
                );
            }

            throw $exception;
        }

        if (!$updated) {
            $this->session->flash(
                'error',
                'Unable to update term.'
            );

            return $this->redirect(
                '/SchoolERP/public/terms/'
                . $id
                . '/edit'
            );
        }

        $this->session->flash(
            'success',
            'Term updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/terms'
        );
    }

    /**
     * Activate a term.
     */
    public function activate(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        if ($this->terms->find($id) === null) {
            return Response::notFound();
        }

        if (!$this->terms->activate($id)) {
            $this->session->flash(
                'error',
                'Unable to activate term.'
            );

            return $this->redirect(
                '/SchoolERP/public/terms'
            );
        }

        $this->session->flash(
            'success',
            'Term activated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/terms'
        );
    }

    /**
     * Deactivate a term.
     */
    public function deactivate(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        if ($this->terms->find($id) === null) {
            return Response::notFound();
        }

        if (!$this->terms->deactivate($id)) {
            $this->session->flash(
                'error',
                'Unable to deactivate term.'
            );

            return $this->redirect(
                '/SchoolERP/public/terms'
            );
        }

        $this->session->flash(
            'success',
            'Term deactivated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/terms'
        );
    }
}