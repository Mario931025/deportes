<div class="card b">
    <div class="card-header bg-gray-lighter text-bold">{{ __('Change Password') }}</div>
    <div class="card-body">
        <form action="{{ route('admin.profile.change-password') }}" method="POST">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label>{{ __('Current Password') }}</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('New Password') }}</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('Confirm New Password') }}</label>
                <input type="password" name="new_password_confirmation" class="form-control" required>
            </div>
            <button class="btn btn-primary" type="submit">{{ __('Change') }}</button>
        </form>
    </div>
</div>