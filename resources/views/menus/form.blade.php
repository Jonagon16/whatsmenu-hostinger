<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($menu) ? 'Edit Menu' : 'Create Menu' }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="menuEditor({{ isset($menu) ? json_encode($menu) : 'null' }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ isset($menu) ? route('menus.update', $menu) : route('menus.store') }}">
                        @csrf
                        @if(isset($menu))
                            @method('PUT')
                        @endif

                        <!-- Basics -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Menu Name</label>
                            <input type="text" name="name" x-model="form.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" x-model="form.is_active" value="1" class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-gray-700">Set as Active Menu</span>
                            </label>
                        </div>

                        <hr class="my-6">

                        <!-- Tree Editor -->
                        <h3 class="text-xl font-bold mb-4">Menu Structure</h3>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Welcome Message</label>
                            <textarea x-model="form.tree.welcome_message" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <div class="border p-4 rounded bg-gray-50 mb-4">
                            <h4 class="font-bold mb-2">Root Options</h4>
                            <template x-for="(node, index) in form.tree.items" :key="index">
                                <div class="bg-white p-3 rounded shadow mb-2 border">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex gap-2 mb-2">
                                                <input type="text" x-model="node.key" placeholder="Option (e.g. 1)" class="border rounded px-2 py-1 w-20 text-sm">
                                                <input type="text" x-model="node.label" placeholder="Label" class="border rounded px-2 py-1 flex-1 text-sm">
                                            </div>
                                            <!-- Children or Response -->
                                            <div class="pl-4 border-l-2 border-gray-200 mt-2">
                                                <div class="mb-2">
                                                    <label class="text-xs font-bold text-gray-500 uppercase">Response</label>
                                                    <textarea x-model="node.response" placeholder="Response text (if no sub-menu)" rows="2" class="border rounded w-full text-sm p-1"></textarea>
                                                </div>
                                                
                                                <!-- Simplified Sub-menu: Only 1 level deep for basic editor initially, or just JSON for complex -->
                                                <!-- Implementing full recursive UI in one go is hard. Let's provide a JSON fallback or simple 1-level nested -->
                                                <div class="mt-2 text-xs text-gray-500">
                                                    <p>Note: Advanced sub-menus editing via JSON below for now.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeNode(form.tree.items, index)" class="text-red-500 hover:text-red-700 ml-2">x</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="addNode(form.tree.items)" class="bg-green-500 text-white px-3 py-1 rounded text-sm mt-2">+ Add Option</button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Goodbye Message</label>
                            <textarea x-model="form.tree.goodbye_message" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <!-- Advanced JSON Mode -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Raw JSON definition (Advanced)</label>
                            <textarea name="tree_json" x-model="jsonTree" rows="10" class="font-mono text-xs shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                            <input type="hidden" name="tree" :value="jsonTree">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ isset($menu) ? 'Update Menu' : 'Create Menu' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function menuEditor(existingMenu) {
            return {
                form: {
                    name: existingMenu ? existingMenu.name : '',
                    is_active: existingMenu ? existingMenu.is_active : false,
                    tree: existingMenu ? existingMenu.tree : {
                        title: 'Menu',
                        welcome_message: '¡Hola! Bienvenido.',
                        goodbye_message: '¡Gracias por contactarnos!',
                        items: []
                    }
                },
                get jsonTree() {
                    return JSON.stringify(this.form.tree, null, 2);
                },
                set jsonTree(value) {
                    try {
                        this.form.tree = JSON.parse(value);
                    } catch(e) {
                        // Invalid JSON
                    }
                },
                addNode(list) {
                    list.push({
                        id: Date.now().toString(),
                        key: (list.length + 1).toString(),
                        label: 'New Option',
                        response: '',
                        children: []
                    });
                },
                removeNode(list, index) {
                    list.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
