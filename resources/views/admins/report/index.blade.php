@extends('layouts.adminApp')

@section('title', 'Plan')

@push('styles')
<style>
    /* Google Font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

    /* Variables and Resets */
    :root {
        --bg-main: #F8F9FA;
        --bg-card: #FFFFFF;
        --text-dark: #1F2937;
        --text-medium: #6B7280;
        --border-color: #E5E7EB;
        --status-pending-bg: #FEF9C3;
        --pagination-active-bg: #F3F4F6;
    }

    /* Styles for the content section */
    .content-wrapper {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-main);
        color: var(--text-dark);
        padding: 2rem;
    }

    .table-container {
        background-color: var(--bg-card);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
    }

    .plans-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .plans-table th,
    .plans-table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .plans-table thead {
        border-bottom: 1px solid var(--border-color);
    }

    .plans-table th {
        font-weight: 500;
        color: var(--text-medium);
        font-size: 0.8rem;
    }

    .plans-table tbody tr {
        border-bottom: 1px solid var(--border-color);
    }

    .plans-table tbody tr:last-child {
        border-bottom: none;
    }

    .plans-table tbody td {
        font-size: 0.875rem;
        color: var(--text-dark);
        font-weight: 500;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 500;
        text-align: center;
    }

    .status-pending {
        background-color: var(--status-pending-bg);
        color: #374151;
    }

    /* Custom Checkbox */
    input[type="checkbox"] {
        width: 1.1rem;
        height: 1.1rem;
        border-radius: 4px;
        border: 2px solid var(--border-color);
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        vertical-align: middle;
        transition: background-color 0.2s, border-color 0.2s;
    }
    input[type="checkbox"]:checked {
        background-color: #4F46E5;
        border-color: #4F46E5;
    }

    input[type="checkbox"]:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
    }

    /* Table Footer */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.5rem;
        font-size: 0.875rem;
        color: var(--text-medium);
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pagination a, .pagination span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-medium);
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .pagination a:hover {
        background-color: #F9FAFB;
        color: var(--text-dark);
    }

    .pagination .active {
        background-color: var(--pagination-active-bg);
        color: var(--text-dark);
        font-weight: 600;
    }

    .pagination .icon {
        width: 1rem;
        height: 1rem;
    }

</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="table-container">
        <table class="plans-table">
            <thead>
                <tr>
                    <th><input type="checkbox" aria-label="Select all plans"></th>
                    <th>Plan Title</th>
                    <th>Type</th>
                    <th>Submitted By</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 7; $i++)
                <tr>
                    <td><input type="checkbox" aria-label="Select plan"></td>
                    <td>7-Day Shred</td>
                    <td>Workout</td>
                    <td>Alex Johnson</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                </tr>
                @endfor
            </tbody>
        </table>
        <div class="table-footer">
            <div class="results-info">
                Showing 1 to 4 of 4 results
            </div>
            <nav class="pagination" aria-label="Pagination">
                <a href="#" aria-label="Previous page">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <a href="#" class="active" aria-current="page">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">4</a>
                <a href="#">5</a>
                <a href="#" aria-label="Next page">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </nav>
        </div>
    </div>
</div>
@endsection
