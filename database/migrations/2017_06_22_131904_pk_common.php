<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PkCommon extends Migration
{
    private $package_name = 'pk_common';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::connection()->getPdo()->exec($this->package());
        DB::connection()->getPdo()->exec($this->package_body());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::connection()->getPdo()->exec("drop package $this->package_name");
    }

    public function package()
    {
        $title = "create or replace package $this->package_name as ";
        $bottom = "end $this->package_name ;";
        $function = [];
        $content = "";

        array_push($function, $this->get_uuid());
        array_push($function, $this->get_md5());
        array_push($function, $this->get_new_file_id());
        array_push($function, $this->get_file_token());

        for ($i = 0 ; $i < count($function); $i++) {
            $content = $content . $function[$i];
        }

        return $title . $content . $bottom;
    }

    public function package_body()
    {
        $title = "create or replace package body $this->package_name as ";
        $bottom = "end $this->package_name ;";
        $function = [];
        $content = "";

        array_push($function, $this->get_uuid(true));
        array_push($function, $this->get_md5(true));
        array_push($function, $this->get_new_file_id(true));
        array_push($function, $this->get_file_token(true));

        for ($i = 0 ; $i < count($function); $i++) {
            $content = $content . $function[$i];
        }

        return $title . $content . $bottom;
    }

    public function get_uuid($content = false)
    {
        $package = "
            function get_uuid return varchar2;
        ";
        $body = "
            -- ----------------------------------------------------------------------------
            function get_uuid return varchar2 is
            -- ----------------------------------------------------------------------------
                guid varchar2(32);
                uuid varchar2(36);
            begin
                guid := sys_guid();
                uuid := substr(guid, 1, 8)||'-'||
                    substr(guid, 9, 4)||'-'||
                    substr(guid, 13, 4)||'-'||
                    substr(guid, 17, 4)||'-'||
                    substr(guid, 21, 12);
                return uuid;
            end get_uuid;
            -- ----------------------------------------------------------------------------
        ";
        return $content ? $body : $package; 
    }

    public function get_md5($content = false)
    {
        $package = "
            function get_md5 (v_str in varchar2) return varchar2;
        ";
        $body = "
            -- ----------------------------------------------------------------------------
            function get_md5 (v_str in varchar2) return varchar2 is
            -- ----------------------------------------------------------------------------
                v_encrypt varchar2(250);
            begin
                v_encrypt := dbms_obfuscation_toolkit.md5(input => utl_raw.cast_to_raw(v_str));
                return v_encrypt;
            end get_md5;
            -- ----------------------------------------------------------------------------
        ";
        return $content ? $body : $package;
    }

    public function get_new_file_id($content = false)
    {
        $package = "
            procedure get_new_file_id (v_name in varchar2, v_discription in varchar2, 
                v_user in varchar2, v_previous in varchar2, 
                r_id out varchar2, r_result out varchar2, r_msg out varchar2);
        ";
        $body = "
            -- ----------------------------------------------------------------------------
            procedure get_new_file_id (v_name in varchar2, v_discription in varchar2, 
                v_user in varchar2, v_previous in varchar2, 
                r_id out varchar2, r_result out varchar2, r_msg out varchar2) is
            -- ----------------------------------------------------------------------------
                v_id varchar2(32);
            begin
                v_id := sys_guid();
                insert into api_file_base 
                    values (v_id, v_name, v_discription, v_previous, 'C', v_user, '', CURRENT_TIMESTAMP, '');

                insert into api_file_code (file_id, created_by, created_at)
                    values (v_id, v_user, CURRENT_TIMESTAMP);
                r_id := v_id;
                r_result := 'true';
                r_msg := 'create file data success !!';
            exception
                when others then
                    r_id := '';
                    r_result := 'false';
                    r_msg := SQLCODE||' -ERROR- '||SQLERRM;
            end get_new_file_id;
            -- ----------------------------------------------------------------------------
        ";
        return $content ? $body : $package;
    }

    public function get_file_token($content = false)
    {
        $package = "
            procedure get_file_token (v_file_id in varchar2, v_user in varchar2,  
                r_token out varchar2, r_user out varchar2, 
                r_file_id out varchar2, r_result out varchar2, r_msg out varchar2);
        ";
        $body = "
            -- ----------------------------------------------------------------------------
            procedure get_file_token (v_file_id in varchar2, v_user in varchar2,  
                r_token out varchar2, r_user out varchar2, 
                r_file_id out varchar2, r_result out varchar2, r_msg out varchar2) is
            -- ----------------------------------------------------------------------------
                v_token varchar2(32);
                c_file_id varchar2(32);
                c_user varchar2(32);
            begin
                v_token := sys_guid();
                c_file_id := get_md5(v_file_id);
                c_user := get_md5(v_user);
                insert into api_file_token 
                    values (v_token, c_user, c_file_id, 'G', v_user, '', CURRENT_TIMESTAMP, '');
                r_token := v_token;
                r_file_id := c_file_id;
                r_user := c_user;
                r_result := 'true';
                r_msg := 'create file token success !!';
            exception
                when others then
                    r_token := '';
                    r_result := 'false';
                    r_msg := SQLCODE||' -ERROR- '||SQLERRM;
            end get_file_token;
            -- ----------------------------------------------------------------------------
        ";
        return $content ? $body : $package;
    }

    public function set_upload_file_data($content = false)
    {
        $package = "
            procedure set_upload_file_data (v_id in varchar2, v_user in varchar2, 
                v_name in varchar2, v_extension in varchar2, v_mime in varchar2, v_code in clob,  
                r_result out varchar2, r_msg out varchar2);
        ";
        $body = "
            -- ----------------------------------------------------------------------------
            procedure set_upload_file_data (v_id in varchar2, v_user in varchar2, 
                v_name in varchar2, v_extension in varchar2, v_mime in varchar2, v_code in clob,  
                r_result out varchar2, r_msg out varchar2) is
            -- ----------------------------------------------------------------------------
            begin
                update api_file_code 
                    set name = v_name, extension = v_extension, mime = v_mime,
                        updated_by = v_user, updated_at = CURRENT_TIMESTAMP
                    where file_id = v_id;

            
                update api_file_base
                    set status = 'S', updated_by = v_user
                    where id = v_id;
                
                r_result := 'true';
                r_msg := 'upload file data success !!';
            exception
                when others then
                    r_result := 'false';
                    r_msg := SQLCODE||' -ERROR- '||SQLERRM;
            end set_upload_file_data;
            -- ----------------------------------------------------------------------------
        ";
        return $content ? $body : $package;
    }

    public function get_file_code($content = false)
    {
        $package = "
            procedure get_file_code (v_token in varchar2, v_file_id in varchar2, v_user in varchar2, 
                r_name out varchar2, r_extension out varchar2, r_mime out varchar2, r_code out clob, 
                r_result out varchar2, r_msg out varchar2);
        ";
        $body = "
            -- ----------------------------------------------------------------------------
            procedure get_file_code (v_token in varchar2, v_file_id in varchar2, v_user in varchar2, 
                r_name out varchar2, r_extension out varchar2, r_mime out varchar2, r_code out clob, 
                r_result out varchar2, r_msg out varchar2) is
            -- ----------------------------------------------------------------------------
                c_file_id varchar2(32);
                c_user varchar2(32);
                v_name varchar2(30);
                v_extension varchar2(30);
                v_mime varchar2(30);
                v_code clob;
                v_status varchar2(1);
                e_status exception;
                e_permission exception;
                created_user varchar2(10);
            begin
                select t.file_id, t.load_user, t.status, c.name, c.extension, c.mime, c.code, t.created_by
                    into c_file_id, c_user, v_status, v_name, v_extension, v_mime, v_code, created_user
                    from api_file_token t, api_file_code c
                    where file_token = v_token and t.file_id = c.file_id;
            
                if v_status != 'G' then
                    raise_application_error(-20010, 'the data status is not G');
                end if;
            
                if (c_file_id != v_file_id) or (c_user != v_user) or (get_md5(created_user) != v_user) then
                    raise_application_error(-20020, 'You do not have permission to read the file');
                end if;
                
                update api_file_token
                    set status = 'L', updated_by = created_user, updated_at = CURRENT_TIMESTAMP
                    where file_token = v_token;
                    
                r_name := v_name;
                r_extension := v_extension;
                r_mime := v_mime;
                r_code := v_code;
                r_result := 'true';
                r_msg := 'create file token success !!';
            exception
                when others then
                    r_code := '';
                    r_name := '';
                    r_extension := '';
                    r_mime := '';
                    r_result := 'false';
                    r_msg := SQLCODE||' -ERROR- '||SQLERRM;
            end get_file_code;
            -- ----------------------------------------------------------------------------
        ";
        return $content ? $body : $package;
    }
}
