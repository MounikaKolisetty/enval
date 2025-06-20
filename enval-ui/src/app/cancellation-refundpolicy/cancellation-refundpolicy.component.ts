import { Component } from '@angular/core';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { FooterComponent } from '../footer/footer.component';
import { NavbarComponent } from '../navbar/navbar.component';

@Component({
  selector: 'app-cancellation-refundpolicy',
  standalone: true,
  imports: [ScrollButtonComponent,
            FooterComponent,
            NavbarComponent
  ],
  templateUrl: './cancellation-refundpolicy.component.html',
  styleUrl: './cancellation-refundpolicy.component.css'
})
export class CancellationRefundpolicyComponent {

}
