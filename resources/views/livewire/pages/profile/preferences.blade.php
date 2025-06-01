{{-- resources/views/livewire/pages/profile/preferences.blade.php --}}

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">Preferences</h3>
        <button type="button" 
                wire:click="resetAllPreferences"
                wire:confirm="Are you sure you want to reset all preferences to their default values?"
                class="text-sm text-gray-500 hover:text-red-600 transition-colors">
            Reset All to Defaults
        </button>
    </div>
    
    <form wire:submit="savePreferences" class="space-y-6">
        @php
            $preferenceManager = app(\App\Services\UserPreferenceManager::class);
            $definitions = $preferenceManager->getDefinitions();
        @endphp

        @foreach($definitions as $groupKey => $groupData)
            <div class="bg-gray-50 rounded-lg p-6 relative">
                {{-- Group Reset Button --}}
                <button type="button" 
                        wire:click="resetGroupPreferences('{{ $groupKey }}')"
                        wire:confirm="Are you sure you want to reset {{ $groupData['title'] }} preferences to defaults?"
                        class="absolute top-4 right-4 text-xs text-gray-400 hover:text-red-500 transition-colors">
                    Reset
                </button>

                <h4 class="text-md font-medium text-gray-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $preferenceManager->getIconSvg($groupData['icon']) !!}
                    </svg>
                    {{ $groupData['title'] }}
                </h4>
                
                @if($groupData['description'])
                    <p class="text-sm text-gray-600 mb-4">{{ $groupData['description'] }}</p>
                @endif
                
                <div class="space-y-4">
                    @foreach($groupData['fields'] as $fieldKey => $fieldData)
                        @if($fieldData['type'] === 'boolean')
                            <label class="flex items-center justify-between p-3 bg-white rounded-lg border hover:bg-gray-50 cursor-pointer transition-colors">
                                <div class="flex-1 mr-4">
                                    <span class="text-sm font-medium text-gray-700">{{ $fieldData['label'] }}</span>
                                    <p class="text-xs text-gray-500">{{ $fieldData['description'] }}</p>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" 
                                           wire:model="preferences.{{ $groupKey }}.{{ $fieldKey }}" 
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                            </label>
                        @elseif($fieldData['type'] === 'select')
                            <div class="p-3 bg-white rounded-lg border">
                                <label for="{{ $groupKey }}_{{ $fieldKey }}" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $fieldData['label'] }}
                                </label>
                                <p class="text-xs text-gray-500 mb-2">{{ $fieldData['description'] }}</p>
                                <select wire:model="preferences.{{ $groupKey }}.{{ $fieldKey }}" 
                                        id="{{ $groupKey }}_{{ $fieldKey }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                                    @foreach($fieldData['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($fieldData['type'] === 'number' || $fieldData['type'] === 'integer')
                            <div class="p-3 bg-white rounded-lg border">
                                <label for="{{ $groupKey }}_{{ $fieldKey }}" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $fieldData['label'] }}
                                </label>
                                <p class="text-xs text-gray-500 mb-2">{{ $fieldData['description'] }}</p>
                                <input type="number" 
                                       wire:model="preferences.{{ $groupKey }}.{{ $fieldKey }}" 
                                       id="{{ $groupKey }}_{{ $fieldKey }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       @if(isset($fieldData['min'])) min="{{ $fieldData['min'] }}" @endif
                                       @if(isset($fieldData['max'])) max="{{ $fieldData['max'] }}" @endif>
                            </div>
                        @elseif($fieldData['type'] === 'text' || $fieldData['type'] === 'string')
                            <div class="p-3 bg-white rounded-lg border">
                                <label for="{{ $groupKey }}_{{ $fieldKey }}" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $fieldData['label'] }}
                                </label>
                                <p class="text-xs text-gray-500 mb-2">{{ $fieldData['description'] }}</p>
                                <input type="text" 
                                       wire:model="preferences.{{ $groupKey }}.{{ $fieldKey }}" 
                                       id="{{ $groupKey }}_{{ $fieldKey }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="{{ $fieldData['placeholder'] ?? '' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Save Preferences Button --}}
        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                Changes are automatically validated and cached for better performance
            </div>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span wire:loading.remove wire:target="savePreferences">Save Preferences</span>
                <span wire:loading wire:target="savePreferences" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>