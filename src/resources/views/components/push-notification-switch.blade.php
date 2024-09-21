@props(['label' => 'allowed Notification', 'id' => 'toggle' . uniqid()])
<label
    {{ $attributes->merge([
        'class' => 'relative inline-flex cursor-pointer items-center',
        'for' => $id,
    ]) }}
    x-data="{
        push_notification: $persist(false),
        token: null,
        isLoading: false,
        init() {
            this.checkNotificationStatus();
    
        },
        async toggleNotification($el) {
    
            console.log($el.checked);
    
            if ($el.checked) {
                return await this.enableNotification();
            } else {
                return await this.disableNotification();
            }
        },
        async checkNotificationStatus() {
    
            if (this.push_notification) return;
    
            if (Notification.permission === 'granted') {
                $store.fcm.getPermission(async (data) => {
                    const { token } = data;
                    this.token = token;
                    try {
                        this.isLoading = true;
                        const response = await fetch('{{ route('fcm-notification.check') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ token: this.token }),
                        });
                        const data = await response.json();
                        console.log(data)
                        this.push_notification = data.allowed;
                    } catch (error) {
                        console.error('Error checking notification status', error);
                    } finally {
                        this.isLoading = false;
                    }
                })
            }
        },
    
        async enableNotification() {
    
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                $store.fcm.getPermission(async (data) => {
                    const { os, token } = data;
                    this.token = token;
                    this.os = os;
                    try {
                        this.isLoading = true;
                        const response = await fetch('{{ route('fcm-notification.enable') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ token: this.token, os: this.os }),
                        });
                        const data = await response.json();
                        console.log(data)
                        this.push_notification = data.success ? true : false;
                    } catch (error) {
                        console.error('Error enabling notification', error);
                    } finally {
                        this.isLoading = false;
                    }
                });
    
            }
        },
    
        async disableNotification() {
    
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                $store.fcm.getPermission(async (data) => {
                    const { os, token } = data;
                    this.token = token;
                    this.os = os;
                    try {
                        this.isLoading = true;
                        const response = await fetch('{{ route('fcm-notification.disable') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ token: this.token }),
                        });
                        const data = await response.json();
                        console.log(data)
                        this.push_notification = data.success ? false : true;
                    } catch (error) {
                        console.error('Error disabling notification', error);
                    } finally {
                        this.isLoading = false;
                    }
                });
    
            }
        },
    }">
    ,
    <input class="peer sr-only" id="{{ $id }}" type="checkbox" @change='toggleNotification($el)'
        x-model='push_notification'>
    <div
        class="peer h-6 w-11 rounded-full bg-gray-300 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-5 peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
    </div>
    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $label }}</span>
</label>
