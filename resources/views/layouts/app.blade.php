<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Task Manager' }}</title>

        @vite('resources/css/app.css')
        @livewireStyles
        
        <!-- Fallback CDN Tailwind for development -->
        @if(app()->environment('local'))
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        
        <!-- Confetti for celebrations -->
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        {{ $slot }}

        <!-- Notification Container -->
        <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        @vite('resources/js/app.js')
        @livewireScripts
        
        <!-- Task Manager Event Handlers -->
        <script>
            // Notification System
            function showNotification(message, type = 'success') {
                const container = document.getElementById('notification-container');
                const notification = document.createElement('div');
                
                const bgColor = type === 'success' ? 'bg-green-500' : 
                               type === 'error' ? 'bg-red-500' : 
                               type === 'info' ? 'bg-blue-500' : 'bg-gray-500';
                
                const icon = type === 'success' ? '✅' : 
                            type === 'error' ? '❌' : 
                            type === 'info' ? 'ℹ️' : '📢';
                
                notification.innerHTML = `
                    <div class="flex items-center p-4 ${bgColor} text-white rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full opacity-0">
                        <span class="text-lg mr-3">${icon}</span>
                        <span class="flex-1">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;
                
                container.appendChild(notification);
                
                // Animate in
                setTimeout(() => {
                    notification.firstElementChild.classList.remove('translate-x-full', 'opacity-0');
                }, 100);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.firstElementChild.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => notification.remove(), 300);
                    }
                }, 5000);
            }

            // Task Manager Event Listeners
            document.addEventListener('livewire:init', () => {
                // Success notifications
                Livewire.on('notify', (message) => {
                    showNotification(message, 'success');
                    console.log('✅ Success:', message);
                });

                // Error notifications
                Livewire.on('notify-error', (message) => {
                    showNotification(message, 'error');
                    console.error('❌ Error:', message);
                });

                // Task created event
                Livewire.on('task-created', (taskId) => {
                    showNotification(`Task berhasil dibuat! ID: ${taskId}`, 'success');
                    console.log('📝 Task Created:', taskId);
                    
                    // Optional: Add confetti effect or sound
                    if (window.confetti) {
                        confetti({
                            particleCount: 100,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    }
                });

                // Task completed event
                Livewire.on('task-completed', (taskId) => {
                    showNotification(`Task selesai! Kerja bagus! 🎉`, 'success');
                    console.log('✅ Task Completed:', taskId);
                });

                // Tag created event
                Livewire.on('tag-created', (tagId) => {
                    showNotification(`Tag baru berhasil dibuat!`, 'info');
                    console.log('🏷️ Tag Created:', tagId);
                });

                // Tags updated event
                Livewire.on('tags-updated', (count) => {
                    console.log(`🏷️ Tags Updated: ${count} tags selected`);
                    
                    // Visual feedback for tag selection
                    if (count > 0) {
                        const tagButton = document.querySelector('[wire\\:click*="showTagsDropdown"]');
                        if (tagButton) {
                            tagButton.classList.add('ring-2', 'ring-blue-500');
                            setTimeout(() => {
                                tagButton.classList.remove('ring-2', 'ring-blue-500');
                            }, 1000);
                        }
                    }
                });

                // Component rendered event
                Livewire.on('component-rendered', () => {
                    console.log('🔄 Component Rendered');
                    
                    // Focus on first input if exists
                    const firstInput = document.querySelector('input[wire\\:model*="newTaskTitle"]');
                    if (firstInput && !firstInput.value) {
                        // Only focus if input is empty to avoid disrupting user
                        setTimeout(() => firstInput.focus(), 100);
                    }
                });

                // Modal state changed event
                Livewire.on('modal-state-changed', () => {
                    console.log('🔄 Modal State Changed');
                    
                    // Add modal backdrop blur effect
                    const body = document.body;
                    const hasOpenModal = document.querySelector('[x-show="open"]');
                    
                    if (hasOpenModal) {
                        body.classList.add('modal-open');
                    } else {
                        body.classList.remove('modal-open');
                    }
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + K to focus search/task input
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const taskInput = document.querySelector('input[wire\\:model*="newTaskTitle"]');
                    if (taskInput) {
                        taskInput.focus();
                        showNotification('Task input focused! Start typing...', 'info');
                    }
                }
                
                // Escape to close all modals/dropdowns
                if (e.key === 'Escape') {
                    // Dispatch close-modals event to Livewire
                    Livewire.dispatch('close-modals');
                }
            });

            // Page visibility change - refresh data when user returns
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    console.log('🔄 Page became visible - refreshing tasks');
                    Livewire.dispatch('refresh-tasks');
                }
            });
        </script>
        
        <!-- Additional CSS for modal backdrop -->
        <style>
            .modal-open {
                overflow: hidden;
            }
            .modal-open::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(2px);
                z-index: 40;
            }
        </style>
    </body>
</html>