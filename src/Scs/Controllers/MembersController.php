<?php
namespace Scs\Controllers;

use Scs\Scs;
use Ng\Core\Managers\Collection;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\LabelAlignment;

class MembersController extends Controller
{

    /* constructeur */
    public function __construct(Scs $app)
    {
        parent::__construct($app);
        $this->loadModel("members");
    }

    /* Page d'acceuil */
    public function index()
    {
        $members = $this->members->all();

        $this->setLayout("default");
        $this->viewRender("base/register", compact("members"));
    }


    /* ajout des informations */
    public function add()
    {
        $infos = new Collection($_POST);

        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST['nom'], $_POST['second_nom'], $_POST['type'], $_POST['description'])  &&
            !empty($_POST['nom']) && !empty($_POST['second_nom']) && !empty($_POST['type']) && !empty($_POST['description'])) {
                $this->validator->isEmpty('nom', 'tous les champs doivent être complétés');
                if ($this->validator->isValid()) {
                    $nom = $this->str::escape($infos->get('nom'));
                    $second_nom = $this->str::escape($infos->get('second_nom'));
                    $type = $this->str::escape($infos->get('type'));
                    $description = $this->str::escape($infos->get('description')) ?? null;

                    $this->members->create(compact("nom", "second_nom", "description", "type"));

                    $qrCode = new QrCode("{$nom} {$second_nom} : {$type} \n\n {$description}");
                    $qrCode->setWriterByName('png');
                    $qrCode->setSize(400);
                    $qrCode->setLabel(
                        'SCS',
                        18,
                        WEBROOT."/assets/fonts/mrsmonster.ttf",
                        LabelAlignment::CENTER
                    );
                    $qrCode->setValidateResult(true);
                    $qrCode->writeFile(WEBROOT."/qrcodes/{$this->members->lastInsertId()}.png");

                    $this->flash->set("success", "Ajout effectué");
                    $this->app::redirect("/");
                } else {
                    $this->flash->set("danger", "Complétez tous les champs");
                }
            }
        }

        $this->setLayout("form");
        $this->viewRender("base/add", compact("infos"));
    }


    /* edition des informations */
    public function edit(int $id)
    {
        if ($this->members->find(intval($id))) {
            $member = $this->members->find(intval($id));
            $infos = new Collection($_POST);

            if (isset($_POST) && !empty($_POST)) {
                $nom = $this->str::escape($infos->get("nom")) ?? $member->nom;
                $second_nom = $this->str::escape($infos->get("second_nom")) ?? $member->nom;
                $type = $this->str::escape($infos->get("type")) ?? $member->type;
                $description = $this->str::escape($infos->get('description')) ?? $member->description;

                $this->members->update($member->id, compact("nom", "second_nom", "description", "type"));

                $qrCode = new QrCode("{$nom} {$second_nom} : {$type}");
                $qrCode->setWriterByName('png');
                $qrCode->setSize(400);
                $qrCode->setLabel(
                    'SCS',
                    18,
                    WEBROOT."/assets/fonts/mrsmonster.ttf",
                    LabelAlignment::CENTER
                );
                $qrCode->setValidateResult(true);
                $qrCode->writeFile(WEBROOT."/qrcodes/{$member->id}.png");


                $this->flash->set("success", "Edition effectuée");
                $this->app::redirect("/");
            }

            $this->setLayout("form");
            $this->viewRender("base/edit", compact("infos", "member"));
        } else {
            $this->flash->set("danger", "Le Membre que vous souhaitez editer n'existe pas ou plus");
            $this->app::redirect(true);
        }
    }


    /* suppression d'un membre */
    public function delete()
    {
        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $id = intval($_POST['id']);

                if ($this->members->find($id)) {
                    $this->members->delete($id);
                    $this->flash->set("success", "Le Membre a bien été supprimer");
                    $this->app::redirect(true);
                } else {
                    $this->flash->set("danger", "Le Membre que vous souhaitez supprimer n'existe pas ou plus");
                    $this->app::redirect(true);
                }
            } else {
                $this->flash->set("danger", "Une erreur est survenue lors de la suppression");
                $this->app::redirect(true);
            }
        } else {
            $this->flash->set("danger", "Une erreur est survenue lors de la suppression");
            $this->app::redirect(true);
        }
    }
}
