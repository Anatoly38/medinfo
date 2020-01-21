<div class="btn-group" @if (count($formsections) === 0) style="display: none" @endif>
    <div id="SectionsManager" class="btn btn-default">
        <div id="FormSections" style="display: none; height: 600px; overflow: auto">
            <table class="table table-hover">
                @foreach($formsections as $formsection)
                    <tr @if(isset($formsection->section_blocks[0]))
                        title="Раздел {{ $formsection->section_blocks[0]->blocked ? 'принят' : 'отклонен' }} {{ $formsection->section_blocks[0]->updated_at }} пользователем {{ $formsection->section_blocks[0]->worker->description }}"
                        class=" {{ $formsection->section_blocks[0]->blocked === true ? 'success' : 'danger' }} "
                        @else
                        title="Статус раздела не менялся"
                        @endif
                        id="{{ $formsection->id }}"
                    >
                        <td>{{ $formsection->section_name }}</td>
                        @if(isset($formsection->section_blocks[0]))
                            <td>
                                <button title="Принять" class="btn btn-default blocksection" id="{{ $formsection->id }}" {{ $formsection->section_blocks[0]->blocked ? 'disabled' : '' }}>
                                    <span class='glyphicon glyphicon-check'></span>
                                </button>
                            </td>
                            <td>
                                <button title="Отклонить" class="btn btn-default unblocksection" id="{{ $formsection->id }}" {{ $formsection->section_blocks[0]->blocked ? '' : 'disabled' }} >
                                    <span class='glyphicon glyphicon-remove'></span>
                                </button>
                            </td>
                        @else
                            <td>
                                <button title="Принять" class="btn btn-default blocksection" id="{{ $formsection->id }}">
                                    <span class='glyphicon glyphicon-check'></span>
                                </button>
                            </td>
                            <td>
                                <button title="Отклонить" class="btn btn-default unblocksection" id="{{ $formsection->id }}" disabled >
                                    <span class='glyphicon glyphicon-remove'></span>
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>