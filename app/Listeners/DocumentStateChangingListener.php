<?php

namespace App\Listeners;

use App\Document;
use Mail;
use App\DocumentMessage;
use Carbon\Carbon;
use App\StatechangingLog;
use App\Events\DocumentStateChanging;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentStateChangingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DocumentStateChanging  $event
     * @return void
     */
    public function handle(DocumentStateChanging $event)
    {
        //
        $worker = $event->happening['worker'];
        $document = $event->happening['document'];
        $old_state = $event->happening['old_state'];
        $new_state = $event->happening['new_state'];
        $remark = $event->happening['remark'];
        $emails = $event->happening['emails'];

        StatechangingLog::create(['worker_id' => $worker->id, 'document_id' => $document->id,
            'oldstate' => $old_state, 'newstate' => $new_state, 'occured_at' => Carbon::now()]);
        $newmessage = new DocumentMessage();
        $newmessage->doc_id = $document->id;
        $newmessage->user_id = $worker->id;
        $newlabel = Document::$state_labels[$document->state];
        $newmessage->message = "Статус документа изменен на \"". $newlabel . "\". " .  $remark;
        $newmessage->save();
        //dd(config('medinfo.permission'));
        $for_mail_body = compact('document', 'remark', 'worker','form', 'current_unit', 'newlabel');
        try {
            Mail::send('emails.changestatemessage', $for_mail_body, function ($m) use ($emails) {
                $m->from(config('medinfo.server_email'), 'Email оповещение Мединфо');
                $m->to($emails)->subject('Изменен статус отчетного документа Мединфо');
            });
            $data['sent_to'] = implode(",", $emails);
        } catch (\Exception $e) {
            $data['sent_to'] = 'Почтовое сообщение о смене статуса документа не доставлено адресатам ' . implode(",", $emails);
            $data['sent_error'] = $e->getMessage();
        }
    }
}