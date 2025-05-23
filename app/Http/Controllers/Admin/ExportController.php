<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabinet;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function exportCabinets()
    {
        $cabinets = Cabinet::with(['companies', 'users'])->get();

        $csvData = "Nom,Raison Sociale,Ville,Statut,Sociétés,Utilisateurs,Date Création\n";

        foreach ($cabinets as $cabinet) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%d,%d,%s\n",
                $cabinet->nom,
                $cabinet->raison_sociale,
                $cabinet->ville,
                $cabinet->statut,
                $cabinet->companies->count(),
                $cabinet->users->count(),
                $cabinet->date_creation->format('d/m/Y')
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="cabinets_' . now()->format('Y-m-d') . '.csv"');
    }

    public function exportCompanies()
    {
        $companies = Company::with('cabinet')->get();

        $csvData = "Raison Sociale,Cabinet,Forme Juridique,ICE,Ville,Statut,Date Constitution\n";

        foreach ($companies as $company) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $company->raison_sociale,
                $company->cabinet->nom,
                $company->forme_juridique,
                $company->ice,
                $company->ville_siege,
                $company->statut,
                $company->date_constitution->format('d/m/Y')
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="societes_' . now()->format('Y-m-d') . '.csv"');
    }

    public function exportUsers()
    {
        $users = User::with('cabinet')->get();

        $csvData = "Nom,Prénom,Email,Cabinet,Rôle,Statut,Date Création\n";

        foreach ($users as $user) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $user->name,
                $user->prenom ?? '',
                $user->email,
                $user->cabinet->nom ?? 'Aucun',
                $user->role_global,
                $user->statut,
                $user->created_at->format('d/m/Y')
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="utilisateurs_' . now()->format('Y-m-d') . '.csv"');
    }
}
