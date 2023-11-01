<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\freelancer;
use App\Models\freelancer_language;
use App\Models\freelancer_skill;
use App\Models\language;
use App\Models\LanguageClass;
use App\Models\occupation;
use App\Models\picture;
use App\Models\skill;
use App\Models\sub_category;
use App\Models\sub_occupation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FreelancerController extends Controller
{
    public function freelancerActivation(Request $request)
    {
        $currentUserId = auth()->id();
        $user = User::find($currentUserId);

        $file1 = $request->file('profileImage');
        $file2 = $request->file('idCardImage');
        $file3 = $request->file('idCardWithSefieImage');

        $picData1 = [
            'filename' => date('YmDHi') . $file1->getClientOriginalName(),
            'filetype' => $file1->getMimeType(),
            'file' => $file1->getContent(),
        ];

        $pic1 = picture::create($picData1);

        $user->name = $request->name;
        $user->picture_id = $pic1->picture_id;
        $user->save();

        $picData2 = [
            'filename' => date('YmDHi') . $file2->getClientOriginalName(),
            'filetype' => $file2->getMimeType(),
            'file' => $file2->getContent(),
        ];
        $picData3 = [
            'filename' => date('YmDHi') . $file3->getClientOriginalName(),
            'filetype' => $file3->getMimeType(),
            'file' => $file3->getContent(),
        ];

        $pic2 = picture::create($picData2);
        $pic3 = picture::create($picData3);

        $freelancer = freelancer::create([
            'user_id' => $currentUserId,
            'identity_number' => $request->niknumber,
            'identity_name' => $request->nikname,
            'identity_gender' => $request->nikgender,
            'identity_address' => $request->nikaddress,
            'description' => $request->description,
            'rating' => 0,
            'revenue' => 0,
            'total_sales' => 0,
            'link' => $request->url,
            'id_card' => $pic2->picture_id,
            'id_card_with_selfie' => $pic3->picture_id,
            'isApproved' => "pending",
        ]);

        $dataStringLanguages = $request->input('languages');
        $dataArray = json_decode($dataStringLanguages);
        foreach ($dataArray as $data) {
            Log::info("$data->language - $data->level");
            $result = language::where('language_name', $data->language)->value('language_id');
            freelancer_language::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'language_id' => $result,
                'proficiency_level' => $data->level,
            ]);
        }

        $dataStringOccupations = $request->input('occupations');
        $occupationData = json_decode($dataStringOccupations);
        Log::info("occu $occupationData->occupation");
        Log::info("from $occupationData->from");
        Log::info("to $occupationData->to");
        $result = category::where('category_name', $occupationData->occupation)->value('category_id');
        occupation::create([
            'freelancer_id' => $freelancer->freelancer_id,
            'category_id' => $result,
            'from' => $occupationData->from,
            'to' => $occupationData->to,
        ]);


        $skillsArray = explode(',', $request->skills);
        foreach ($skillsArray as $data) {
            Log::info($data);
            $record = skill::firstOrCreate(['skill_name' => $data]);
            freelancer_skill::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'skill_id' => $record->skill_id,
            ]);
        }

        $skillsArray = explode(',', $request->subcategoryOccupation);
        foreach ($skillsArray as $data) {
            Log::info($data);
            $result = sub_category::where('subcategory_name', $data)->value('subcategory_id');
            sub_occupation::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'subcategory_id' => $result,
            ]);
        }

        return response([
            'message' => 'Verification request has been successfully created',
        ], 201);
    }
}
