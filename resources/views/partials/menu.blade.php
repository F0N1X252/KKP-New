<style>
    /* Styling Menu Modern */
    .nav-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(255, 255, 255, 0.4);
        font-weight: 800;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        padding-left: 1.75rem;
        font-family: 'Inter', sans-serif;
    }

    .nav-item-link {
        display: flex;
        align-items: center;
        padding: 0.7rem 1.5rem;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        font-weight: 500;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .nav-item-link:hover {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff;
        padding-left: 1.7rem;
    }

    .nav-item-link.active {
        background: linear-gradient(90deg, rgba(255,255,255,0.1), transparent);
        color: #fff;
        border-left-color: #fbbf24;
        font-weight: 600;
    }

    .nav-item-link i {
        font-size: 1.1rem;
        width: 24px;
        margin-right: 12px;
        display: inline-flex;
        justify-content: center;
        transition: transform 0.2s;
    }

    .nav-item-link:hover i {
        transform: scale(1.1) rotate(5deg);
        color: #fbbf24;
    }
</style>

<div class="d-flex flex-column h-100">
    
    <!-- DASHBOARD -->
    <a href="{{ route('admin.home') }}" class="nav-item-link {{ request()->is('admin') ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i>
        <span>{{ trans('global.dashboard') }}</span>
    </a>

    <!-- TICKETING SECTION -->
    <div class="nav-label">Workspace</div>

    @can('ticket_access')
    <a href="{{ route('admin.tickets.index') }}" class="nav-item-link {{ request()->is('admin/tickets*') ? 'active' : '' }}">
        <i class="bi bi-ticket-perforated-fill"></i>
        <span>{{ trans('cruds.ticket.title') }}</span>
    </a>
    @endcan

    @can('comment_access')
    <a href="{{ route('admin.comments.index') }}" class="nav-item-link {{ request()->is('admin/comments*') ? 'active' : '' }}">
        <i class="bi bi-chat-quote-fill"></i>
        <span>{{ trans('cruds.comment.title') }}</span>
    </a>
    @endcan

    <!-- SETTINGS SECTION -->
    <div class="nav-label">Settings</div>

    @can('status_access')
    <a href="{{ route('admin.statuses.index') }}" class="nav-item-link {{ request()->is('admin/statuses*') ? 'active' : '' }}">
        <i class="bi bi-toggle-on"></i>
        <span>{{ trans('cruds.status.title') }}</span>
    </a>
    @endcan

    @can('priority_access')
    <a href="{{ route('admin.priorities.index') }}" class="nav-item-link {{ request()->is('admin/priorities*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-fill"></i>
        <span>{{ trans('cruds.priority.title') }}</span>
    </a>
    @endcan

    @can('category_access')
    <a href="{{ route('admin.categories.index') }}" class="nav-item-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
        <i class="bi bi-tags-fill"></i>
        <span>{{ trans('cruds.category.title') }}</span>
    </a>
    @endcan

    <!-- SYSTEM SECTION -->
    @can('user_management_access')
        <div class="nav-label">System</div>
        
        @can('user_access')
        <a href="{{ route('admin.users.index') }}" class="nav-item-link {{ request()->is('admin/users*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>{{ trans('cruds.user.title') }}</span>
        </a>
        @endcan

        @can('role_access')
        <a href="{{ route('admin.roles.index') }}" class="nav-item-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock-fill"></i>
            <span>{{ trans('cruds.role.title') }}</span>
        </a>
        @endcan

        @can('permission_access')
        <a href="{{ route('admin.permissions.index') }}" class="nav-item-link {{ request()->is('admin/permissions*') ? 'active' : '' }}">
            <i class="bi bi-key-fill"></i>
            <span>{{ trans('cruds.permission.title') }}</span>
        </a>
        @endcan

        @can('audit_log_access')
        <a href="{{ route('admin.audit-logs.index') }}" class="nav-item-link {{ request()->is('admin/audit-logs*') ? 'active' : '' }}">
            <i class="bi bi-terminal-fill"></i>
            <span>{{ trans('cruds.auditLog.title') }}</span>
        </a>
        @endcan
    @endcan
</div>