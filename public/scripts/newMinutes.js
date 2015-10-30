$(document).ready(function() {
    $( "#meetingDate" ).datepicker({
        numberOfMonths: 2
    });

    $( "#meetingDateConfirm" ).datepicker({
        numberOfMonths: 2
    });

    $( "#approvedOn" ).datepicker({
        numberOfMonths: 2
    });

    $( "#approved" ).change(function() {
        $value = $( "#approved").val();

        if($value == 'yes')
        {
            $(' #approvedOn ').prop('disabled', false);
            $(' #approvedOn ').select();
        }
        else
        {
            $(' #approvedOn ').prop('disabled', true);
        }
    });

    CKEDITOR.replace( 'openingComments', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.openingComments.getData();
                $count = CKEDITOR.instances.openingComments.getData().length;

                if($data != "")
                {
                    $('#openingCommentsLink').html("<i class=\"fi-check\"></i> Opening Comments");
                }
                else
                {
                    $('#openingCommentsLink').html("Opening Comments");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#openingCommentsLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#openingCommentsLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'communications', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.communications.getData();
                $count = CKEDITOR.instances.communications.getData().length;

                if($data != "")
                {
                    $('#communicationsLink').html("<i class=\"fi-check\"></i> Communications");
                }
                else
                {
                    $('#communicationsLink').html("Communications");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#communicationsLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#communicationsLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'cityCouncil', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.cityCouncil.getData();
                $count = CKEDITOR.instances.cityCouncil.getData().length;

                if($data != "")
                {
                    $('#cityCouncilLink').html("<i class=\"fi-check\"></i> City Council");
                }
                else
                {
                    $('#cityCouncilLink').html("City Council");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#cityCouncilLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#cityCouncilLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'foundation', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.foundation.getData();
                $count = CKEDITOR.instances.foundation.getData().length;

                if($data != "")
                {
                    $('#foundationLink').html("<i class=\"fi-check\"></i> Foundation");
                }
                else
                {
                    $('#foundationLink').html("Foundation");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#foundationLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#foundationLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'financeBudgeting', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.financeBudgeting.getData();
                $count = CKEDITOR.instances.financeBudgeting.getData().length;

                if($data != "")
                {
                    $('#financeBudgetingLink').html("<i class=\"fi-check\"></i> Finance & Budgeting");
                }
                else
                {
                    $('#financeBudgetingLink').html("Finance & Budgeting");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#financeBudgetLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#financeBudgetLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'buildingGrounds', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.buildingGrounds.getData();
                $count = CKEDITOR.instances.buildingGrounds.getData().length;

                if($data != "")
                {
                    $('#buildingGroundsLink').html("<i class=\"fi-check\"></i> Building & Grounds");
                }
                else
                {
                    $('#buildingGroundsLink').html("Building & Grounds");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#buildingGroundsLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#buildingGroundsLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'planningPublicRelations', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.planningPublicRelations.getData();
                $count = CKEDITOR.instances.planningPublicRelations.getData().length;

                if($data != "")
                {
                    $('#planningPublicRelationsLink').html("<i class=\"fi-check\"></i> Planning & Public Relations");
                }
                else
                {
                    $('#planningPublicRelationsLink').html("Planning & Public Relations");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#planningPublicRelationsLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#planningPublicRelationsLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'operationsPersonnel', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.operationsPersonnel.getData();
                $count = CKEDITOR.instances.operationsPersonnel.getData().length;

                if($data != "")
                {
                    $('#operationsPersonnelLink').html("<i class=\"fi-check\"></i> Operations & Personnel");
                }
                else
                {
                    $('#operationsPersonnelLink').html("Operations/Personnel");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#operationsPersonnelLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#operationsPersonnelLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'businessIssues', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.businessIssues.getData();
                $count = CKEDITOR.instances.businessIssues.getData().length;

                if($data != "")
                {
                    $('#businessIssuesLink').html("<i class=\"fi-check\"></i> Business Issues");
                }
                else
                {
                    $('#businessIssuesLink').html("Business Issues");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#businessIssuesLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#businessIssuesLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'libraryStaff', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.libraryStaff.getData();
                $count = CKEDITOR.instances.libraryStaff.getData().length;

                if($data != "")
                {
                    $('#libraryStaffLink').html("<i class=\"fi-check\"></i> Library Staff Report");
                }
                else
                {
                    $('#libraryStaffLink').html("Library Staff Report");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#libraryStaffLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#libraryStaffLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'libraryDirector', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.libraryDirector.getData();
                $count = CKEDITOR.instances.libraryDirector.getData().length;

                if($data != "")
                {
                    $('#libraryDirectorLink').html("<i class=\"fi-check\"></i> Library Director Report");
                }
                else
                {
                    $('#libraryDirectorLink').html("Library Director Report");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#libraryDirectorLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#libraryDirectorLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'publicComment', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.publicComment.getData();
                $count = CKEDITOR.instances.publicComment.getData().length;

                if($data != "")
                {
                    $('#publicCommentLink').html("<i class=\"fi-check\"></i> Public Comment");
                }
                else
                {
                    $('#publicCommentLink').html("Public Comment");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#publicCommentLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#publicCommentLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    CKEDITOR.replace( 'additionalComments', {
        on: {
            change: function() {
                $data = CKEDITOR.instances.additionalComments.getData();
                $count = CKEDITOR.instances.additionalComments.getData().length;

                if($data != "")
                {
                    $('#additionalCommentsLink').html("<i class=\"fi-check\"></i> Addtional Comments");
                }
                else
                {
                    $('#additionalCommentsLink').html("Additional Comments");
                }

                //Count characters in the field
                $percent = ($count/10000)*100;
                $percent = $percent.toFixed(2);

                if($percent > 100)
                {
                    $('#additionalCommentsLimit').width('100%');
                    $('#limit').foundation('reveal', 'open');
                }
                else
                {
                    $('#additionalCommentsLimit').animate({width: $percent+'%'}, 100);
                }
            }
        }
    } );

    $data = CKEDITOR.instances.openingComments.getData();
    if($data != "")
    {
        $('#openingCommentsLink').html("<i class=\"fi-check\"></i> Opening Comments");
    }
    else
    {
        $('#openingCommentsLink').html("Opening Comments");
    }

    $data = CKEDITOR.instances.communications.getData();
    if($data != "")
    {
        $('#communicationsLink').html("<i class=\"fi-check\"></i> Communications");
    }
    else
    {
        $('#communicationsLink').html("Communications");
    }

    $data = CKEDITOR.instances.cityCouncil.getData();
    if($data != "")
    {
        $('#cityCouncilLink').html("<i class=\"fi-check\"></i> City Council");
    }
    else
    {
        $('#cityCouncilLink').html("City Council");
    }

    $data = CKEDITOR.instances.foundation.getData();
    if($data != "")
    {
        $('#foundationLink').html("<i class=\"fi-check\"></i> Foundation");
    }
    else
    {
        $('#foundationLink').html("Foundation");
    }

    $data = CKEDITOR.instances.financeBudgeting.getData();
    if($data != "")
    {
        $('#financeBudgetingLink').html("<i class=\"fi-check\"></i> Finance & Budgeting");
    }
    else
    {
        $('#financeBudgetingLink').html("Finance & Budgeting");
    }

    $data = CKEDITOR.instances.buildingGrounds.getData();
    if($data != "")
    {
        $('#buildingGroundsLink').html("<i class=\"fi-check\"></i> Building & Grounds");
    }
    else
    {
        $('#buildingGroundsLink').html("Building & Grounds");
    }

    $data = CKEDITOR.instances.planningPublicRelations.getData();
    if($data != "")
    {
        $('#planningPublicRelationsLink').html("<i class=\"fi-check\"></i> Planning & Public Relations");
    }
    else
    {
        $('#planningPublicRelationsLink').html("Planning & Public Relations");
    }

    $data = CKEDITOR.instances.operationsPersonnel.getData();
    if($data != "")
    {
        $('#operationsPersonnelLink').html("<i class=\"fi-check\"></i> Operations & Personnel");
    }
    else
    {
        $('#operationsPersonnelLink').html("Operations & Personnel");
    }

    $data = CKEDITOR.instances.businessIssues.getData();
    if($data != "")
    {
        $('#businessIssuesLink').html("<i class=\"fi-check\"></i> Business Issues");
    }
    else
    {
        $('#businessIssuesLink').html("Business Issues");
    }

    $data = CKEDITOR.instances.libraryStaff.getData();
    if($data != "")
    {
        $('#libraryStaffLink').html("<i class=\"fi-check\"></i> Library Staff");
    }
    else
    {
        $('#libraryStaffLink').html("Library Staff");
    }

    $data = CKEDITOR.instances.libraryDirector.getData();
    if($data != "")
    {
        $('#libraryDirectorLink').html("<i class=\"fi-check\"></i> Library Director");
    }
    else
    {
        $('#libraryDirectorLink').html("Library Director");
    }

    $data = CKEDITOR.instances.publicComment.getData();
    if($data != "")
    {
        $('#publicCommentLink').html("<i class=\"fi-check\"></i> Public Comment");
    }
    else
    {
        $('#publicCommentLink').html("Public Comment");
    }

    $data = CKEDITOR.instances.additionalComments.getData();
    if($data != "")
    {
        $('#additionalCommentsLink').html("<i class=\"fi-check\"></i> Additional Comments");
    }
    else
    {
        $('#additionalCommentsLink').html("Additional Comments");
    }

});