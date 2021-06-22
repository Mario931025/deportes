<div class="card b">
    <div class="card-header bg-gray-lighter text-bold">{{ __('Personal Information') }}</div>
    <div class="card-body">
        <form action="{{ route('admin.profile.personal-information') }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name') ?? $user->name }}" class="form-control" placeholder="{{ __('Name') }}"  required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="lastName">{{ __('Last Name') }}</label>
                    <input type="text" name="last_name" id="lastName" value="{{ old('last_name') ?? $user->last_name }}" class="form-control" placeholder="{{ __('Last Name') }}" required>
                </div>
            </div>
            
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="email">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" value="{{ $user->email }}" class="form-control" placeholder="{{ __('Email') }}" readonly>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="phone">{{ __('Phone') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') ?? $user->phone }}"  class="form-control" placeholder="{{ __('Phone') }}" required>
                </div>
            </div>
            
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="documentNumber">{{ __('Document Number') }}</label>
                    <input type="text" name="document_number" id="documentNumber" value="{{ old('document_number') ?? $user->document_number }}" class="form-control" placeholder="{{ __('Document Number') }}" required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="birthday">{{ __('Birthday') }}</label>
                    <input type="text" name="birthday" id="birthday" value="{{ old('birthday') ?? ($user->birthday ? $user->birthday->format('d-m-Y') : null) }}" class="form-control" placeholder="{{ __('Birthday') }}" required>
                </div>
            </div>
            
            
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="cityId">{{ __('City') }}</label>
                    <select class="form-control" name="city_id" id="cityId">
                        @isset($user->city_id)
                            <option value="{{ $user->city_id }}" selected="selected">{{ $user->city->name }}</option>
                        @endisset
                    </select>
                </div>    

                <div class="form-group col-md-6">
                    <label for="roleId">{{ __('Role') }}</label>
                    <select class="form-control" style="width:100%" name="role_id[]" id="roleId" multiple="multiple" disabled>
                        @if(old('role_id'))
                            @foreach(old('role_id') as $role)             
                                <option value="{{ $role }}" selected>{{ App\Models\Role::find($role)->description }}</option>
                            @endforeach
                        @elseif(isset($user))
                            @foreach($user->roles as $role)             
                                <option value="{{ $role->id }}" selected>{{ $role->description }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>                 
            </div>
            
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="academyId">{{ __('Academy') }}</label>
                    <input type="text" name="academy_id" id="academyId" value="{{ ($user->academy ? $user->academy->name : null) }}" class="form-control" placeholder="{{ __('Academy') }}" readonly>
                </div>
            
                <div class="form-group col-md-6">
                    <label for="gradeId">{{ __('Grade') }}</label>
                    <input type="text" name="grade_id" id="gradeId" value="{{ ($user->grade ? $user->grade->name : null) }}" class="form-control" placeholder="{{ __('Grade') }}" readonly>
                </div> 
            </div>

            <div class="form-group">
                <label for="profilePhoto">{{ __('Profile Photo') }}</label>
                <div class="file-loading">
                    @php
                        $preview = [];
                    
                        if (!empty($user->profile_photo)) {
                            $url = $user->profile_photo;
                            
                            $preview = [
                                'initialPreview' => url('storage/'. $url),
                                'initialPreviewAsData' => true,
                                'initialPreviewConfig' => [
                                    [
                                        'caption' => $user->name,
                                        'filename' => $user->name,
                                        'downloadUrl' => url('storage/'. $url),
                                        'size' => Storage::disk('public')->size($url),
                                    ],
                                ],
                                'deleteUrl' => route('admin.users.profile-photo.delete', $user->id),
                                'deleteExtraData' => ['_token' => csrf_token()],
                                'ajaxDeleteSettings' => ['type' => 'DELETE'],
                                'overwriteInitial' => true,  
                            ];
                        }            
                    @endphp
                    <input name="profile_photo" id="profilePhoto" type="file" data-preview='@json($preview)'>
                </div>
            </div>                                 
            

            <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('[name="birthday"]').datetimepicker({
            locale: 'es',
            format: 'DD-MM-YYYY',       
        });        
        
        $('[name="city_id"]').select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/cities/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('City') }}",
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1,
            templateResult: function(data) {        
                if (data.loading) { return data.text; }
                var markup = data.text;
                return markup;
            },
            templateSelection: function(data) { return data.text; }
        });
        
        $('[name="role_id[]"]').select2({
            theme: "bootstrap4",
            ajax: {
                url: "{{ route('admin.roles.filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('Role') }}",
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1,
            templateResult: function(data) {        
                if (data.loading) { return data.text; }
                var markup = data.text;
                return markup;
            },
            templateSelection: function(data) { return data.text; }
        });         

        var option = {
            language: "es",
            theme: "fas",
            allowedFileExtensions: ["jpg", "jpeg", "png", "gif"],
            browseClass: "btn btn-primary",
            showCaption: false,
            showRemove: true,
            showUpload: false,
            showCancel: false,
            showClose: false,
            maxImageHeight: 150,
            maxFileCount: 1,
            resizeImage: true            
        };
        
        var preview = $("[name='profile_photo']").data('preview');
        
        jQuery.extend(option, preview);
    
        $("[name='profile_photo']").fileinput(option);
    });
</script>
@endpush