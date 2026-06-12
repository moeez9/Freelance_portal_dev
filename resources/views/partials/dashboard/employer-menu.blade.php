<div class="inner scrollbar_custom max-h-full py-6 px-3">
    <div class="area">
        <span class="px-6 text-xs font-semibold text-secondary uppercase">Overviews</span>
        <ul class="list_link flex flex-col gap-2 mt-2">
            <li>
                <a href="{{ route('employer.dashboard') }}" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">
                    <span class="ph-duotone ph-squares-four text-2xl text-secondary"></span>
                    <strong class="text-title">Dashboard</strong>
                </a>
            </li>
            <li>
                <a href="{{ route('messages.index') }}" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background {{ ($active ?? '') === 'messages' ? 'active' : '' }}">
                    <span class="ph-duotone ph-chats text-2xl text-secondary"></span>
                    <strong class="text-title">Messages</strong>
                </a>
            </li>
            <li>
                <a href="{{ route('notifications.index') }}" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background {{ ($active ?? '') === 'notifications' ? 'active' : '' }}">
                    <span class="ph-duotone ph-bell text-2xl text-secondary"></span>
                    <strong class="text-title">Notifications</strong>
                </a>
            </li>
        </ul>
    </div>
    <div class="area mt-6">
        <span class="px-6 text-xs font-semibold text-secondary uppercase">Management</span>
        <ul class="list_link flex flex-col gap-2 mt-2">
            <li>
                <a href="{{ route('employer.applicants') }}" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background {{ ($active ?? '') === 'applicants' ? 'active' : '' }}">
                    <span class="ph-duotone ph-notepad text-2xl text-secondary"></span>
                    <strong class="text-title">Applicants Jobs</strong>
                </a>
            </li>
            <li>
                <a href="{{ route('employer.jobs.index') }}" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background {{ ($active ?? '') === 'jobs' ? 'active' : '' }}">
                    <span class="ph-duotone ph-briefcase text-2xl text-secondary"></span>
                    <strong class="text-title">My Jobs</strong>
                </a>
            </li>
            <li>
                <a href="{{ route('employer.jobs.create') }}" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background {{ ($active ?? '') === 'jobs_create' ? 'active' : '' }}">
                    <span class="ph-duotone ph-file-arrow-up text-2xl text-secondary"></span>
                    <strong class="text-title">Submit Jobs</strong>
                </a>
            </li>
            <li>
                <a href="{{ route('profile.edit') }}" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background {{ ($active ?? '') === 'profile' ? 'active' : '' }}">
                    <span class="ph-duotone ph-user-circle text-2xl text-secondary"></span>
                    <strong class="text-title">Profile Setup</strong>
                </a>
            </li>
        </ul>
    </div>
    <div class="area mt-6">
        <span class="px-6 text-xs font-semibold text-secondary uppercase">User</span>
        <ul class="list_link flex flex-col gap-2 mt-2">
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="link flex items-center gap-3 w-full py-3 px-6 rounded-lg duration-300 hover:bg-background text-left">
                        <span class="ph-duotone ph-sign-out text-2xl text-secondary"></span>
                        <strong class="text-title">Log Out</strong>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
