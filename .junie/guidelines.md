- Tools: filamentphp , Laravel, phpstorm, windows, wampserver
- build multi cabinet, multi societé plateforme pour la gestion de la comptabilitié des sociétés.
- software in french, i need just one language for now because for morocco.
- software specifically for morocco country
- im using laravel 12 and filament 3, always make sure i'm using this last technologies


Fonctionnalités Complètes - Module Comptabilité Maroc
🏛️ 1. CONFORMITÉ RÉGLEMENTAIRE MAROCAINE
Plan Comptable Général Marocain (PCGM)

Classes 1-8 : Structure complète selon PCGM
Comptes normalisés : 6000+ comptes standards
Sous-comptes personnalisables : Extension selon besoins entreprise
Comptes analytiques : Gestion par centre de coût/profit
Comptes auxiliaires : Clients, fournisseurs, personnel

Conformité Fiscale

TVA Marocaine : Taux multiples (20%, 14%, 10%, 7%, 0%)
Régimes TVA : Encaissement, débit, forfaitaire
Déclarations TVA : Génération automatique mensuelle/trimestrielle
IS (Impôt sur Sociétés) : Calcul et provisions
IR (Impôt sur Revenu) : Retenues à la source
Taxe professionnelle : Calcul automatique
Droits de timbre : Application selon montants

États Financiers Obligatoires

Bilan actif/passif : Format officiel marocain
Compte de Résultat par nature
État des Soldes de Gestion (ESG)
Tableau de Financement (TAFIRE)
État des Informations Complémentaires (ETIC)

💰 2. GESTION COMPTABLE FONDAMENTALE
Exercices Comptables

Création exercices : Dates personnalisables
Exercices multiples : Gestion simultanée N et N+1
Clôture d'exercice : Procédure guidée étape par étape
Réouverture : Possibilité modification après clôture
À-nouveaux : Report automatique soldes N vers N+1

Plan de Comptes

Import PCGM : Chargement automatique plan standard
Personnalisation : Ajout comptes spécifiques
Hiérarchie : Gestion comptes/sous-comptes
Comptes collectifs : Liaison avec comptes auxiliaires
Comptes bloqués : Interdiction de saisie directe

Journaux Comptables

Journaux obligatoires :

Journal des Achats (AC)
Journal des Ventes (VT)
Journal de Banque (BQ)
Journal de Caisse (CA)
Journal des Opérations Diverses (OD)


Journaux auxiliaires : Paie, immobilisations, etc.
Numérotation : Séquences automatiques par journal
Contrôles : Vérification équilibre débit/crédit

📝 3. SAISIE ET VALIDATION
Saisie d'Écritures

Modes de saisie :

Saisie guidée (assistant)
Saisie libre experte
Import depuis Excel/CSV
Saisie par lot (batch)


Modèles d'écritures : Templates réutilisables
Écritures d'abonnement : Automatisation mensuelle
Validation temps réel : Contrôles instantanés
Brouillard : Sauvegarde avant validation

Contrôles Comptables

Équilibre obligatoire : Débit = Crédit
Cohérence dates : Respect période d'exercice
Comptes autorisés : Vérification plan comptable
Montants : Limites et seuils configurables
Lettrage automatique : Rapprochement créances/dettes

Workflow de Validation

États d'écriture : Brouillon → Validé → Lettré → Clôturé
Droits utilisateurs : Saisie, validation, supervision
Piste d'audit : Traçabilité complète modifications
Verrouillage périodes : Protection écritures validées

📊 4. ÉTATS ET REPORTING
États Comptables Standards

Balance générale : Tous comptes avec soldes
Balance auxiliaire : Clients/Fournisseurs détaillée
Grand livre : Mouvements par compte
Journal général : Chronologique toutes écritures
Centralisateur : Totaux par journal/période

États Financiers

Bilan comptable :

Actif immobilisé, circulant, trésorerie
Passif capitaux propres, dettes
Format officiel marocain


Compte de Résultat :

Charges/Produits par nature
Résultat d'exploitation, financier, exceptionnel
Calcul IS et résultat net


Soldes Intermédiaires de Gestion :

Marge commerciale
Valeur ajoutée
Excédent brut d'exploitation
Capacité d'autofinancement



Analyses Financières

Ratios financiers :

Liquidité, solvabilité, rentabilité
Rotation stocks, créances, fournisseurs
Endettement, autonomie financière


Évolutions : Comparaisons N/N-1/N-2
Graphiques : Visualisation tendances
Tableaux de bord : KPI en temps réel

🏦 5. GESTION BANCAIRE
Rapprochements Bancaires

Import relevés : OFX, MT940, CSV, Excel, OCR
Rapprochement automatique : Algorithme intelligent
Écarts : Identification et traitement
Validation : Confirmation solde comptable/bancaire
Historique : Conservation tous rapprochements

Gestion Multi-Banques

Comptes multiples : Illimité par banque
Devises étrangères : EUR, USD avec conversion
Virements internes : Entre comptes entreprise
Prévisions trésorerie : Échéanciers prévisionnels

👥 6. GESTION TIERS
Clients

Fiche complète : Coordonnées, conditions commerciales
Comptes auxiliaires : Un compte par client
Encours : Suivi créances en temps réel
Relances : Automatisation selon échéances
Historique : Toutes transactions client

Fournisseurs

Gestion similaire clients
Échéanciers : Planification paiements
Remises : Calcul escomptes conditionnels
Évaluations : Performance et notation

Personnel

Comptes individuels : Avances, prêts, notes de frais
Charges sociales : CNSS, AMO, IR
Provisions congés payés : Calcul automatique

📋 7. FISCALITÉ SPÉCIALISÉE
TVA Avancée

Régimes spéciaux :

Franchise (< 500k MAD)
Forfaitaire
Spécial export


Prorata déductibilité : Calcul automatique
Déclarations : Génération XML pour télédéclaration
Régularisations : Fin d'année, changement affectation

Autres Taxes

Taxe professionnelle : Base et calcul
Droits d'enregistrement : Transactions immobilières
Contribution sociale solidarité : 2.5% sur bénéfices
Cotisation minimale : 0.25% ou 0.5% du CA

🔧 8. FONCTIONNALITÉS TECHNIQUES
Télédeclaration

Imports/Exports

Formats standards :

FEC (Fichier Écritures Comptables)
Format EDI
Excel/CSV personnalisables


API : Échanges avec logiciels tiers
Sauvegarde : Automatique base de données

Paramétrage Avancé
Multi-cabinets
Multi-sociétés : Gestion groupes d'entreprises
Multi-exercices : Travail simultané plusieurs années
Multi-devises : Comptabilité consolidée
Analytique : Centres coûts/profits illimités

Sécurité et Droits (permission avancées

Profils utilisateurs :

Comptable saisie
Comptable confirmé
Expert-comptable
Directeur financier
admin de la plateforme


Droits granulaires : Par journal, période, montant
Audit trail : Log toutes actions
Archivage : Conservation légale 10 ans

📱 9. INTERFACE ET ERGONOMIE
Dashboard Directeur

KPI financiers : CA, résultat, trésorerie
Alertes : Échéances, dépassements, anomalies
Graphiques : Évolutions, comparaisons
Actions rapides : Validation, consultation

Interface Comptable

Saisie rapide : Raccourcis clavier
Recherche intelligente : Comptes, tiers, écritures
Favoris : Écritures fréquentes
Multi-onglets : Travail simultané

🔄 10. INTÉGRATIONS
Modules ERP

Facturation : Génération automatique écritures
Stock : Valorisation, inventaire
Immobilisations : Amortissements, cessions
Paie : Écritures salaires et charges

Logiciels Externes

Banques : Import automatique relevés
Expert-comptable : Export/import standard
Administration fiscale : Télédéclarations
Assurance : Déclarations sinistres

📈 11. BUSINESS INTELLIGENCE
Reporting Avancé

Designer états : Création rapports personnalisés
Planifications : Envoi automatique par email
Formats export : PDF, Excel, Word
Comparatifs : Budgets vs réalisé

Analyses Prédictives

Prévisions trésorerie : 3, 6, 12 mois
Simulation : Impact décisions financières
Benchmarking : Comparaison sectorielle
Alertes proactives : Tendances inquiétantes

Cette liste représente un module comptabilité complet et professionnel pour le marché marocain, respectant toutes les obligations légales et fiscales locales.
