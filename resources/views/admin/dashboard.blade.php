@extends('layouts.app')

@section('title', 'Tableau de bord - Administration | SugnuHotel')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="admin-dashboard">
    {{-- En-tête --}}
    <div class="dashboard-header">
        <div class="container">
            <h1>Tableau de bord</h1>
            <p>Bienvenue, {{ Auth::user()->name }} !</p>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="container" style="margin-top: -40px;">
        {{-- Première ligne de statistiques --}}
        <div class="stats-grid">
            {{-- Carte: Chambres totales --}}
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-info">
                    <p>Total chambres</p>
                    <h3>{{ $totalRooms ?? 0 }}</h3>
                </div>
            </div>
            
            {{-- Carte: Chambres disponibles --}}
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <p>Disponibles</p>
                    <h3>{{ $availableRooms ?? 0 }}</h3>
                </div>
            </div>
            
            {{-- Carte: Chambres occupées --}}
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <p>Occupées</p>
                    <h3>{{ $occupiedRooms ?? 0 }}</h3>
                </div>
            </div>
            
            {{-- Carte: Maintenance --}}
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-info">
                    <p>Maintenance</p>
                    <h3>{{ $maintenanceRooms ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        {{-- Deuxième ligne de statistiques --}}
        <div class="stats-secondary">
            {{-- Taux d'occupation --}}
            <div class="stats-card">
                <div class="stats-header">
                    <h4>Taux d'occupation</h4>
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stats-value">{{ $occupancyRate ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill"style="width: {{ $occupancyRate ?? 0 }}%;"></div>
                </div>
            </div>
            
            {{-- Arrivées du jour --}}
            <div class="stats-card">
                <div class="stats-header">
                    <h4>Arrivées aujourd'hui</h4>
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-value">{{ $todayArrivals ?? 0 }}</div>
                <a href="#" class="stats-link">
                    Voir les détails <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            {{-- Départs du jour --}}
            <div class="stats-card">
                <div class="stats-header">
                    <h4>Départs aujourd'hui</h4>
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stats-value">{{ $todayDepartures ?? 0 }}</div>
                <a href="#" class="stats-link">
                    Voir les détails <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        {{-- Graphiques et tableaux --}}
        <div class="charts-grid">
            {{-- Réservations par statut --}}
            <div class="chart-card">
                <h3>Réservations par statut</h3>
                <div class="status-list">
                    <div class="status-item">
                        <div class="status-label">
                            <span class="status-dot pending"></span>
                            <span>En attente</span>
                        </div>
                        <span class="status-count">{{ $pendingReservations ?? 0 }}</span>
                    </div>
                    <div class="status-item">
                        <div class="status-label">
                            <span class="status-dot confirmed"></span>
                            <span>Confirmées</span>
                        </div>
                        <span class="status-count">{{ $confirmedReservations ?? 0 }}</span>
                    </div>
                    <div class="status-item">
                        <div class="status-label">
                            <span class="status-dot checked-in"></span>
                            <span>Check-in effectué</span>
                        </div>
                        <span class="status-count">{{ $checkedInReservations ?? 0 }}</span>
                    </div>
                    <div class="status-item">
                        <div class="status-label">
                            <span class="status-dot checked-out"></span>
                            <span>Terminées</span>
                        </div>
                        <span class="status-count">{{ $checkedOutReservations ?? 0 }}</span>
                    </div>
                    <div class="status-item">
                        <div class="status-label">
                            <span class="status-dot cancelled"></span>
                            <span>Annulées</span>
                        </div>
                        <span class="status-count">{{ $cancelledReservations ?? 0 }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Chiffre d'affaires --}}
            <div class="revenue-card">
                <h3>Chiffre d'affaires</h3>
                <div class="revenue-highlight">
                    <p>Ce mois-ci</p>
                    <div class="amount">{{ number_format($monthlyRevenue ?? 0, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="revenue-stats">
                    <div class="revenue-stat">
                        <p>Total</p>
                        <div class="value">{{ number_format($totalRevenue ?? 0, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="revenue-stat">
                        <p>Clients</p>
                        <div class="value">{{ $totalUsers ?? 0 }}</div>
                    </div>
                    <div class="revenue-stat">
                        <p>Nouveaux</p>
                        <div class="value">{{ $newUsersThisMonth ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Dernières réservations --}}
        <div class="recent-bookings">
            <div class="bookings-header">
                <h3>Dernières réservations</h3>
                <a href="#" class="view-all">Voir tout <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>N° Réservation</th>
                            <th>Client</th>
                            <th>Chambre</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReservations ?? [] as $reservation)
                        @php
                            $statusColors = [
                                'pending' => 'pending',
                                'confirmed' => 'confirmed',
                                'checked_in' => 'checked-in',
                                'checked_out' => 'checked-out',
                                'cancelled' => 'cancelled'
                            ];
                            $statusLabels = [
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'checked_in' => 'Check-in',
                                'checked_out' => 'Terminée',
                                'cancelled' => 'Annulée'
                            ];
                        @endphp
                        <tr>
                            <td>{{ $reservation->reservation_number }}</td>
                            <td>{{ $reservation->user->name ?? 'N/A' }}</td>
                            <td>{{ $reservation->room->room_number ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</td>
                            <td style="font-weight: 600;">{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="status-badge {{ $statusColors[$reservation->status] ?? 'checked-out' }}">
                                    {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                </span>
                            </td>
                            <td class="booking-actions">
                                <a href="#"><i class="fas fa-eye"></i></a>
                                <a href="#"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="empty-message">
                                Aucune réservation récente
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Actions rapides --}}
        <div class="quick-actions">
            <a href="#" class="quick-action-card">
                <i class="fas fa-plus-circle"></i>
                <h4>Nouvelle chambre</h4>
            </a>
            
            <a href="#" class="quick-action-card">
                <i class="fas fa-calendar-plus"></i>
                <h4>Nouvelle réservation</h4>
            </a>
            
            <a href="#" class="quick-action-card">
                <i class="fas fa-user-plus"></i>
                <h4>Nouvel utilisateur</h4>
            </a>
            
            <a href="#" class="quick-action-card">
                <i class="fas fa-cog"></i>
                <h4>Paramètres</h4>
            </a>
        </div>
    </div>
</div>
@endsection