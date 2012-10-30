<%@ Page Title="" Language="C#" MasterPageFile="~/NativeHavens.Master" AutoEventWireup="true" CodeBehind="Contact.aspx.cs" Inherits="NativeHavens.Contact" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="ContentPlaceHolder1" runat="server">
</asp:Content>
<asp:Content ID="Content3" ContentPlaceHolderID="ContentPlaceHolder2" runat="server">
    <table width="100%" cellpadding="1">
        <tr>
            <td align="center">
                <h1>Feedback and Information Request</h1>
            </td>
        </tr>
        <tr>
            <td>
                <h3>
                    <font face="Arial, Helvetica, sans-serif" color="#ff0000">Contact Information</font></h3>
                <table width="480" style="border-collapse: collapse" cellpadding="3" cellspacing="0"
                    border="0">
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em><font color="#990000">First Name:</font></em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtFirstName" runat="server" Width="273"></asp:TextBox>
                                <asp:RequiredFieldValidator ID="Requiredfieldvalidator1" runat="server" ControlToValidate="txtFirstName"
                                    ErrorMessage="" Display="dynamic">*
                                </asp:RequiredFieldValidator>
                            </font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em><font color="#990000">Last Name:</font></em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtLastName" runat="server" Width="273"></asp:TextBox>
                                <asp:RequiredFieldValidator ID="Requiredfieldvalidator2" runat="server" ControlToValidate="txtLastName"
                                    ErrorMessage="" Display="dynamic">*
                                </asp:RequiredFieldValidator>
                            </font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>Title:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtTitle" runat="server" Width="273px"></asp:TextBox></font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>Company:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtCompany" runat="server" Width="273px"></asp:TextBox></font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>Address:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtAddress" runat="server" Width="273px"></asp:TextBox></font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>City:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtCity" runat="server" Width="273px"></asp:TextBox></font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>State:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:DropDownList ID="cboState" runat="server">
                                    <asp:ListItem Value="NA"></asp:ListItem>
                                    <asp:ListItem Value="AL">Alabama</asp:ListItem>
                                    <asp:ListItem Value="AK">Alaska</asp:ListItem>
                                    <asp:ListItem Value="AZ">Arizona</asp:ListItem>
                                    <asp:ListItem Value="AR">Arkansas</asp:ListItem>
                                    <asp:ListItem Value="CA">California</asp:ListItem>
                                    <asp:ListItem Value="CO">Colorado</asp:ListItem>
                                    <asp:ListItem Value="CT">Connecticut</asp:ListItem>
                                    <asp:ListItem Value="DC">D.C.</asp:ListItem>
                                    <asp:ListItem Value="DE">Delaware</asp:ListItem>
                                    <asp:ListItem Value="FL">Florida</asp:ListItem>
                                    <asp:ListItem Value="GA">Georgia</asp:ListItem>
                                    <asp:ListItem Value="HI">Hawaii</asp:ListItem>
                                    <asp:ListItem Value="ID">Idaho</asp:ListItem>
                                    <asp:ListItem Value="IL">Illinois</asp:ListItem>
                                    <asp:ListItem Value="IN">Indiana</asp:ListItem>
                                    <asp:ListItem Value="IA">Iowa</asp:ListItem>
                                    <asp:ListItem Value="KS">Kansas</asp:ListItem>
                                    <asp:ListItem Value="KY">Kentucky</asp:ListItem>
                                    <asp:ListItem Value="LA">Louisiana</asp:ListItem>
                                    <asp:ListItem Value="ME">Maine</asp:ListItem>
                                    <asp:ListItem Value="MD">Maryland</asp:ListItem>
                                    <asp:ListItem Value="MA">Massachusetts</asp:ListItem>
                                    <asp:ListItem Value="MI">Michigan</asp:ListItem>
                                    <asp:ListItem Value="MN">Minnesota</asp:ListItem>
                                    <asp:ListItem Value="MS">Mississippi</asp:ListItem>
                                    <asp:ListItem Value="MO">Missouri</asp:ListItem>
                                    <asp:ListItem Value="MT">Montana</asp:ListItem>
                                    <asp:ListItem Value="NE">Nebraska</asp:ListItem>
                                    <asp:ListItem Value="NV">Nevada</asp:ListItem>
                                    <asp:ListItem Value="NH">New Hampshire</asp:ListItem>
                                    <asp:ListItem Value="NJ">New Jersey</asp:ListItem>
                                    <asp:ListItem Value="NM">New Mexico</asp:ListItem>
                                    <asp:ListItem Value="NY">New York</asp:ListItem>
                                    <asp:ListItem Value="NC">North Carolina</asp:ListItem>
                                    <asp:ListItem Value="ND">North Dakota</asp:ListItem>
                                    <asp:ListItem Value="OH">Ohio</asp:ListItem>
                                    <asp:ListItem Value="OK">Oklahoma</asp:ListItem>
                                    <asp:ListItem Value="OR">Oregon</asp:ListItem>
                                    <asp:ListItem Value="PA">Pennsylvania</asp:ListItem>
                                    <asp:ListItem Value="RI">Rhode Island</asp:ListItem>
                                    <asp:ListItem Value="SC">South Carolina</asp:ListItem>
                                    <asp:ListItem Value="SD">South Dakota</asp:ListItem>
                                    <asp:ListItem Value="TN">Tennessee</asp:ListItem>
                                    <asp:ListItem Value="TX">Texas</asp:ListItem>
                                    <asp:ListItem Value="UT">Utah</asp:ListItem>
                                    <asp:ListItem Value="VT">Vermont</asp:ListItem>
                                    <asp:ListItem Value="VA">Virginia</asp:ListItem>
                                    <asp:ListItem Value="WA">Washington</asp:ListItem>
                                    <asp:ListItem Value="WV">West Virginia</asp:ListItem>
                                    <asp:ListItem Value="WI">Wisconsin</asp:ListItem>
                                    <asp:ListItem Value="WY">Wyoming</asp:ListItem>
                                </asp:DropDownList>
                            </font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>Zip:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtZip" runat="server"></asp:TextBox></font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>Telephone:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtTel" runat="server" Width="273px"></asp:TextBox></font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>Fax:</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtFax" runat="server" Width="273px"></asp:TextBox></font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><font color="#990000"><em>E-mail</em></font></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:TextBox ID="txtEmail" runat="server" Width="273px"></asp:TextBox>
                                <asp:RegularExpressionValidator ID="valRegEx" runat="server" ControlToValidate="txtEmail"
                                    ValidationExpression=".*@.*\..*" ErrorMessage="" Display="dynamic">*
                                </asp:RegularExpressionValidator>
                                <asp:RequiredFieldValidator ID="valRequired" runat="server" ControlToValidate="txtEmail"
                                    ErrorMessage="" Display="dynamic">*
                                </asp:RequiredFieldValidator>
                            </font>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="150">
                            <font face="Arial, Helvetica"><em>How did you find us?</em></font>
                        </td>
                        <td width="330">
                            <font face="Arial, Helvetica">
                                <asp:DropDownList ID="ddlHow" runat="server">
                                </asp:DropDownList>
                            </font>
                        </td>
                    </tr>
                </table>
                <h3>
                    <font face="Arial, Helvetica, sans-serif" color="#ff0000">Comments</font></h3>
                <p>
                </p>
                <asp:TextBox ID="txtComments" runat="server" Width="424px" TextMode="MultiLine" Height="72px"></asp:TextBox>
                <p>
                    <asp:Button ID="cmdSend" runat="server" Text="Submit" onclick="cmdSend_Click"></asp:Button>
                </p>
            </td>
        </tr>
    </table>
</asp:Content>
