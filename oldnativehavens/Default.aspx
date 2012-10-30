<%@ Page Title="" Language="C#" MasterPageFile="~/NativeHavens.Master" AutoEventWireup="true"
    CodeBehind="Default.aspx.cs" Inherits="NativeHavens.Default" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="ContentPlaceHolder1" runat="server">
    <table width="800" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td><img src="images/welcome.jpg" width="206" height="444" alt="Native Havens Landscaping" border="0" usemap="#welcome" /></td>
            <td><img src="images/home_pic_1.jpg" width="594" height="444" alt="Native Havens Landscaping" /></td>
        </tr>
    </table>
    <map name="welcome">
    <area shape="circle" coords="40,400,35" href="http://www.plna.com/content/?/certifications/pch" target="_blank" alt="PCH Landscaping Certification">
    </map>
</asp:Content>
